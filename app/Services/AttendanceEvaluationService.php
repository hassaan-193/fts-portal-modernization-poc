<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AttendanceEvaluationService
{
    /**
     * Evaluate daily attendance for a user
     * Called at end of day via scheduled job
     *
     * @param int $userId - User ID from users table
     * @param Carbon $date - The date to evaluate (defaults to today)
     * 
     * @return array [
     *     'success' => bool,
     *     'user_id' => int,
     *     'date' => string (Y-m-d),
     *     'status' => 'present' | 'absent',
     *     'total_hours' => float,
     *     'total_minutes' => int,
     *     'sessions_count' => int,
     *     'has_late' => bool,
     *     'late_sessions' => array,
     *     'message' => string
     * ]
     */
    public function evaluateDay($userId, $date = null)
    {
        try {
            $date = $date ?? Carbon::today();
            $user = User::findOrFail($userId);

            // Get all sessions for this user on this date
            $sessions = AttendanceSession::where('user_id', $userId)
                ->whereDate('session_date', $date)
                ->where('session_status', 'closed') // Only completed sessions
                ->orderBy('clock_in_time', 'asc')
                ->get();

            // Determine attendance status
            // Present if at least one session exists, Absent if no sessions
            $status = $sessions->count() > 0 ? 'present' : 'absent';

            // Calculate total hours and minutes
            $totalMinutes = $sessions->sum('duration_minutes') ?? 0;
            $totalHours = round($totalMinutes / 60, 2);

            // Check for late sessions
            $hasLate = $sessions->where('is_late', true)->count() > 0;
            $lateSessions = $sessions->where('is_late', true)->map(function ($session) {
                return [
                    'shift' => $session->shift_window,
                    'clock_in_time' => $session->clock_in_time->format('H:i:s'),
                    'is_late' => $session->is_late,
                ];
            })->toArray();

            return [
                'success' => true,
                'user_id' => $userId,
                'user_name' => $user->name,
                'date' => $date->format('Y-m-d'),
                'status' => $status,
                'total_hours' => $totalHours,
                'total_minutes' => $totalMinutes,
                'sessions_count' => $sessions->count(),
                'has_late' => $hasLate,
                'late_sessions' => $lateSessions,
                'message' => $status === 'present' 
                    ? "Present - {$sessions->count()} session(s), {$totalHours} hours worked" 
                    : "Absent - No sessions recorded",
            ];

        } catch (\Exception $e) {
            Log::error('AttendanceEvaluationService@evaluateDay error: ' . $e->getMessage());
            return [
                'success' => false,
                'user_id' => $userId,
                'date' => $date ? $date->format('Y-m-d') : null,
                'status' => null,
                'total_hours' => 0,
                'total_minutes' => 0,
                'sessions_count' => 0,
                'has_late' => false,
                'late_sessions' => [],
                'message' => 'Error evaluating attendance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Batch evaluate attendance for all users for a specific date
     * Used in scheduled jobs to evaluate all users at end of day
     *
     * @param Carbon $date - The date to evaluate (defaults to today)
     * 
     * @return array [
     *     'success' => bool,
     *     'date' => string (Y-m-d),
     *     'evaluated_count' => int,
     *     'present_count' => int,
     *     'absent_count' => int,
     *     'results' => array of evaluation results
     * ]
     */
    public function evaluateDayForAllUsers($date = null)
    {
        try {
            $date = $date ?? Carbon::today();

            // Get all unique users who have sessions on this date
            $userIds = AttendanceSession::whereDate('session_date', $date)
                ->distinct('user_id')
                ->pluck('user_id')
                ->toArray();

            $results = [];
            $presentCount = 0;
            $absentCount = 0;

            // Evaluate each user
            foreach ($userIds as $userId) {
                $evaluation = $this->evaluateDay($userId, $date);
                if ($evaluation['success']) {
                    $results[] = $evaluation;
                    if ($evaluation['status'] === 'present') {
                        $presentCount++;
                    } else {
                        $absentCount++;
                    }
                }
            }

            return [
                'success' => true,
                'date' => $date->format('Y-m-d'),
                'evaluated_count' => count($results),
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'results' => $results,
                'message' => "Evaluated {$presentCount} present and {$absentCount} absent for {$date->format('Y-m-d')}",
            ];

        } catch (\Exception $e) {
            Log::error('AttendanceEvaluationService@evaluateDayForAllUsers error: ' . $e->getMessage());
            return [
                'success' => false,
                'date' => $date ? $date->format('Y-m-d') : null,
                'evaluated_count' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'results' => [],
                'message' => 'Error in batch evaluation: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get attendance summary for a user for a date range
     *
     * @param int $userId - User ID
     * @param Carbon $startDate - Start date (inclusive)
     * @param Carbon $endDate - End date (inclusive)
     * 
     * @return array Summary data with daily breakdowns
     */
    public function getUserAttendanceSummary($userId, $startDate, $endDate)
    {
        try {
            $user = User::findOrFail($userId);

            // Get all sessions in the date range
            $sessions = AttendanceSession::where('user_id', $userId)
                ->whereDate('session_date', '>=', $startDate)
                ->whereDate('session_date', '<=', $endDate)
                ->where('session_status', 'closed')
                ->orderBy('session_date', 'asc')
                ->get()
                ->groupBy(function ($session) {
                    return $session->session_date instanceof \DateTimeInterface
                        ? $session->session_date->format('Y-m-d')
                        : (string) $session->session_date;
                });

            $totalMinutes = 0;
            $totalDays = 0;
            $presentDays = 0;
            $lateDays = 0;
            $dailyBreakdown = [];

            // Process each day
            $sessions->each(function ($daySessions, $dateStr) use (&$totalMinutes, &$totalDays, &$presentDays, &$lateDays, &$dailyBreakdown) {
                $dayMinutes = $daySessions->sum('duration_minutes') ?? 0;
                $dayHours = round($dayMinutes / 60, 2);
                $hasLate = $daySessions->where('is_late', true)->count() > 0;

                $totalMinutes += $dayMinutes;
                $totalDays++;
                $presentDays++;
                if ($hasLate) {
                    $lateDays++;
                }

                $dailyBreakdown[] = [
                    'date' => $dateStr,
                    'sessions' => $daySessions->count(),
                    'hours' => $dayHours,
                    'minutes' => $dayMinutes,
                    'has_late' => $hasLate,
                ];
            });

            $totalHours = round($totalMinutes / 60, 2);

            return [
                'success' => true,
                'user_id' => $userId,
                'user_name' => $user->name,
                'period' => "{$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}",
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => 0, // Not calculated from sessions
                'late_days' => $lateDays,
                'total_hours' => $totalHours,
                'total_minutes' => $totalMinutes,
                'daily_breakdown' => $dailyBreakdown,
                'message' => "Summary for {$user->name}: {$presentDays} days present, {$totalHours} hours worked",
            ];

        } catch (\Exception $e) {
            Log::error('AttendanceEvaluationService@getUserAttendanceSummary error: ' . $e->getMessage());
            return [
                'success' => false,
                'user_id' => $userId,
                'message' => 'Error fetching attendance summary: ' . $e->getMessage(),
            ];
        }
    }
}
