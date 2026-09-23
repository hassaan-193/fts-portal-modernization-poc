<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AttendanceReportService
{
    /**
     * Generate monthly attendance summary for all tracked users.
     *
     * Returns a collection of per-user summaries containing late count,
     * absent days, and detailed breakdowns suitable for the email template.
     *
     * @param  Carbon|null $month  Any date within the target month (defaults to previous month)
     * @return array{month: string, year: int, start: string, end: string, users: Collection}
     */
    public function generateMonthlySummary(?Carbon $month = null): array
    {
        $month = $month ?? Carbon::now()->subMonthNoOverflow();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $excludedIds = config('attendance.excluded_user_ids', []);
        $users = User::whereNotIn('id', $excludedIds)->orderBy('name')->get();

        $allSessionsFlat = AttendanceSession::whereBetween('session_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d'),
            ])
            ->whereNotIn('user_id', $excludedIds)
            ->get();

        $allSessions = $allSessionsFlat->groupBy('user_id');

        $weekdays = $this->getWorkingDays($startOfMonth, $endOfMonth);

        // Determine which Saturdays are "ON" globally:
        // A Saturday is ON if ANY user (across the whole company) has at least one record on that day.
        $onSaturdays = $allSessionsFlat
            ->filter(fn ($s) => $s->session_date->isSaturday())
            ->pluck('session_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->unique();

        // Effective working days = Mon-Fri + globally-ON Saturdays
        $workingDays = $weekdays->merge($onSaturdays)->unique()->sort()->values();

        $userSummaries = $users->map(function ($user) use ($allSessions, $workingDays, $startOfMonth, $endOfMonth) {
            $userSessions = $allSessions->get($user->id, collect());
            return $this->buildUserMonthlySummary($user, $userSessions, $workingDays, $startOfMonth, $endOfMonth);
        });

        return [
            'month' => $startOfMonth->format('F'),
            'year' => $startOfMonth->year,
            'start' => $startOfMonth->format('Y-m-d'),
            'end' => $endOfMonth->format('Y-m-d'),
            'users' => $userSummaries,
        ];
    }

    /**
     * Build a single user's monthly attendance summary.
     *
     * $workingDays already includes globally-ON Saturdays (any user had a record),
     * so if this user has no record on an ON Saturday they are marked absent.
     */
    private function buildUserMonthlySummary(
        User $user,
        Collection $sessions,
        Collection $workingDays,
        Carbon $startOfMonth,
        Carbon $endOfMonth
    ): array {
        $sessionsByDate = $sessions->groupBy(fn ($s) => $s->session_date->format('Y-m-d'));

        $morningLateDates = collect();
        $eveningLateDates = collect();
        $absentDates = collect();

        foreach ($workingDays as $dateStr) {
            $date = Carbon::parse($dateStr);
            $daySessions = $sessionsByDate->get($dateStr, collect());

            if ($daySessions->isEmpty()) {
                $absentDates->push($dateStr);
                continue;
            }

            if ($date->isSaturday()) {
                // Saturday only has a morning shift
                $this->evaluateSaturdayLate($daySessions, $morningLateDates, $dateStr);
            } else {
                $this->evaluateWeekdayShiftLate($daySessions, $morningLateDates, $eveningLateDates, $absentDates, $dateStr);
            }
        }

        return [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'morning_late' => $morningLateDates->count(),
            'evening_late' => $eveningLateDates->count(),
            'total_late' => $morningLateDates->count() + $eveningLateDates->count(),
            'total_absent' => $absentDates->count(),
            'total_days_worked' => $sessionsByDate->count(),
            'total_sessions' => $sessions->count(),
        ];
    }

    /**
     * Evaluate late arrival on a Saturday.
     *
     * Saturday late = any session clocked in after 9:00 AM.
     */
    private function evaluateSaturdayLate(Collection $daySessions, Collection &$lateDates, string $dateStr): void
    {
        $satConfig = config('attendance.saturday');
        $lateHour = $satConfig['late_hour'];
        $lateMinute = $satConfig['late_minute'];

        $firstSession = $daySessions->sortBy('clock_in_time')->first();
        if ($firstSession && $firstSession->clock_in_time) {
            $clockIn = $firstSession->clock_in_time->copy()->setTimezone('Asia/Dubai');
            $isLate = $clockIn->hour > $lateHour
                || ($clockIn->hour === $lateHour && $clockIn->minute > $lateMinute);
            if ($isLate) {
                $lateDates->push($dateStr);
            }
        }
    }

    /**
     * Evaluate weekday absence and per-shift late arrivals.
     *
     * Absent  = no clock-in for BOTH Shift 1 AND Shift 2 on that day.
     * Morning = shift_1 session with is_late true (late from 8:01 AM; Mon–Fri incl. Friday).
     * Evening = shift_2 session with is_late true (after 2:45 PM).
     */
    private function evaluateWeekdayShiftLate(
        Collection $daySessions,
        Collection &$morningLateDates,
        Collection &$eveningLateDates,
        Collection &$absentDates,
        string $dateStr
    ): void {
        $shift1Sessions = $daySessions->where('shift_window', 'shift_1');
        $shift2Sessions = $daySessions->where('shift_window', 'shift_2');

        if ($shift1Sessions->isEmpty() && $shift2Sessions->isEmpty()) {
            $absentDates->push($dateStr);
            return;
        }

        if ($shift1Sessions->where('is_late', true)->isNotEmpty()) {
            $morningLateDates->push($dateStr);
        }

        if ($shift2Sessions->where('is_late', true)->isNotEmpty()) {
            $eveningLateDates->push($dateStr);
        }
    }

    /**
     * Get all standard working days (Mon-Fri) in a date range.
     * Sundays are the weekly day off – always excluded.
     * Saturdays are excluded here; ON Saturdays are determined globally
     * in generateMonthlySummary() based on whether any user has records.
     */
    private function getWorkingDays(Carbon $start, Carbon $end): Collection
    {
        $days = collect();
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            if ($date->isSunday()) {
                continue;
            }
            if ($date->isSaturday()) {
                // Saturdays are handled per-user based on whether they have records
                continue;
            }
            $days->push($date->format('Y-m-d'));
        }

        return $days;
    }
}
