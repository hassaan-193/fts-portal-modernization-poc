<?php

namespace App\Services;

use App\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AttendanceSessionService
{
    protected $geoValidationService;
    protected $shiftRuleService;

    public function __construct(
        GeoValidationService $geoValidationService,
        ShiftRuleService $shiftRuleService
    ) {
        $this->geoValidationService = $geoValidationService;
        $this->shiftRuleService = $shiftRuleService;
    }

    /**
     * Process QR scan for clock-in or clock-out
     * Uses authenticated user ID directly
     * 
     * Simple logic:
     * - If open session exists → Clock-out
     * - If no open session → Clock-in
     * 
     * Handles both scenarios:
     * 1. Split shift: Clock-in (Shift 1) → Clock-out → Clock-in (Shift 2) → Clock-out
     * 2. Continuous: Clock-in (Shift 1) → Clock-out at evening without break
     *
     * @param int $userId - User ID from authentication
     * @param float $latitude - Current latitude
     * @param float $longitude - Current longitude
     * 
     * @return array [
     *     'success' => bool,
     *     'action' => 'clock_in' | 'clock_out',
     *     'session' => AttendanceSession or null,
     *     'message' => string
     * ]
     */
    public function processQRScan($userId, $latitude, $longitude)
    {
        try {
            $geofenceEnabled = config('attendance.geofence_enabled', true);
            $distance = 0;

            if ($geofenceEnabled) {
                $geoValidation = $this->geoValidationService->validateLocation($latitude, $longitude);

                if (!$geoValidation['is_valid']) {
                    return [
                        'success' => false,
                        'action' => null,
                        'session' => null,
                        'message' => 'Employee location is outside allowed radius: ' . $geoValidation['message'],
                    ];
                }

                $distance = $geoValidation['distance_meters'];
            }

            $today = Carbon::today();

            // Check if there's an open session today
            $openSession = AttendanceSession::where('user_id', $userId)
                ->whereDate('session_date', $today)
                ->where('session_status', 'open')
                ->first();

            if ($openSession) {
                return $this->clockOut($openSession, $latitude, $longitude, $distance);
            } else {
                return $this->clockIn($userId, $today, $latitude, $longitude, $distance);
            }

        } catch (\Exception $e) {
            Log::error('AttendanceSessionService@processQRScan error: ' . $e->getMessage());
            return [
                'success' => false,
                'action' => null,
                'session' => null,
                'message' => 'Error processing QR scan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create a clock-in session
     *
     * Shift detection:
     * - Saturday: single shift 9:00 AM – 1:00 PM, late after 9:00 AM
     * - Friday: Shift 1 (before 1:30 PM, late from 8:01 AM — same as Mon–Thu) / Shift 2 (1:30 PM+, late after 2:00 PM)
     * - Mon–Thu: Shift 1 (before 2 PM, late from 8:01 AM) / Shift 2 (2 PM+, late after 2:45 PM)
     */
    private function clockIn($userId, $date, $latitude, $longitude, $distance)
    {
        try {
            $now = now();

            // IMPORTANT: Use Dubai timezone (UTC+4)
            $nowDubai = $now->copy()->setTimezone('Asia/Dubai');
            $hour = $nowDubai->hour;
            $minute = $nowDubai->minute;
            $timeInMinutes = $hour * 60 + $minute;

            $isLate = false;
            $lateMessage = '';

            if ($nowDubai->isSaturday()) {
                $shift = 'shift_1';
                $shiftName = 'Saturday';

                $satLateHour = config('attendance.saturday.late_hour', 9);
                $satLateMinute = config('attendance.saturday.late_minute', 0);
                $isLate = $hour > $satLateHour || ($hour === $satLateHour && $minute > $satLateMinute);

                $lateMessage = $isLate
                    ? "Clock-in at {$nowDubai->format('H:i')} is LATE (Saturday deadline: 9:00 AM)"
                    : "Clock-in at {$nowDubai->format('H:i')} is ON-TIME (Saturday)";
            } elseif ($nowDubai->isFriday() && $timeInMinutes >= 13 * 60 + 30) {
                // Friday: Shift 2 starts at 1:30 PM
                $shift = 'shift_2';
                $shiftName = 'Shift 2 (Friday)';

                $isLate = $hour > 14 || ($hour === 14 && $minute > 0);
                $lateMessage = $isLate
                    ? "Clock-in at {$nowDubai->format('H:i')} is LATE (Friday on-time deadline: 2:00 PM)"
                    : "Clock-in at {$nowDubai->format('H:i')} is ON-TIME (Shift 2, Friday)";
            } elseif ($hour < 14) {
                $shift = 'shift_1';
                $shiftName = 'Shift 1';

                $isLate = $hour > 8 || ($hour === 8 && $minute > 0);
                $lateMessage = $isLate
                    ? "Clock-in at {$nowDubai->format('H:i')} is LATE (late from 8:01 AM)"
                    : "Clock-in at {$nowDubai->format('H:i')} is ON-TIME";
            } else {
                $shift = 'shift_2';
                $shiftName = 'Shift 2';

                $isLate = $hour > 14 || ($hour === 14 && $minute > 45);
                $lateMessage = $isLate
                    ? "Clock-in at {$nowDubai->format('H:i')} is LATE (on-time deadline: 2:45 PM)"
                    : "Clock-in at {$nowDubai->format('H:i')} is ON-TIME (Shift 2)";
            }

            // IMPORTANT: Store clock_in_time in Dubai timezone (UTC+4)
            $clockInTimeDubai = $nowDubai->copy();

            $session = AttendanceSession::create([
                'user_id' => $userId,
                'session_date' => $date,
                'clock_in_time' => $clockInTimeDubai,
                'clock_in_latitude' => $latitude,
                'clock_in_longitude' => $longitude,
                'clock_in_distance_meters' => $distance,
                'shift_window' => $shift,
                'is_late' => $isLate,
                'work_mode' => 'split_shift',
                'session_status' => 'open',
            ]);

            return [
                'success' => true,
                'action' => 'clock_in',
                'session' => $session,
                'message' => "Clock-in successful. {$lateMessage}",
            ];

        } catch (\Exception $e) {
            Log::error('AttendanceSessionService@clockIn error: ' . $e->getMessage());
            return [
                'success' => false,
                'action' => null,
                'session' => null,
                'message' => 'Error during clock-in: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update open session with clock-out
     * 
     * No time window restrictions - accept checkout at any time
     */
    private function clockOut($session, $latitude, $longitude, $distance)
    {
        try {
            $now = now();
            
            // Update session with clock-out details
            // IMPORTANT: Store clock_out_time in Dubai timezone (UTC+4) for consistency
            $clockOutTimeDubai = $now->copy()->setTimezone('Asia/Dubai');
            
            $session->clock_out_time = $clockOutTimeDubai;
            $session->clock_out_latitude = $latitude;
            $session->clock_out_longitude = $longitude;
            $session->clock_out_distance_meters = $distance;
            $session->session_status = 'closed';

            // Calculate duration
            $session->calculateDuration();
            $session->save();

            // Format duration
            $hours = floor($session->duration_minutes / 60);
            $minutes = $session->duration_minutes % 60;
            $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";

            return [
                'success' => true,
                'action' => 'clock_out',
                'session' => $session,
                'message' => "Clock-out successful. Session duration: {$durationText}",
            ];

        } catch (\Exception $e) {
            Log::error('AttendanceSessionService@clockOut error: ' . $e->getMessage());
            return [
                'success' => false,
                'action' => null,
                'session' => null,
                'message' => 'Error during clock-out: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get today's sessions for a user
     */
    public function getTodaysSessionsForUser($userId)
    {
        return AttendanceSession::where('user_id', $userId)
            ->whereDate('session_date', Carbon::today())
            ->orderBy('clock_in_time', 'asc')
            ->get();
    }

    /**
     * Get open session for a user today (if any)
     */
    public function getOpenSessionToday($userId)
    {
        return AttendanceSession::where('user_id', $userId)
            ->whereDate('session_date', Carbon::today())
            ->where('session_status', 'open')
            ->first();
    }

    /**
     * Calculate total hours from sessions
     */
    public function calculateTotalHours($sessions)
    {
        $totalMinutes = $sessions->sum('duration_minutes') ?? 0;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return [
            'total_minutes' => $totalMinutes,
            'hours' => $hours,
            'minutes' => $minutes,
            'formatted' => "{$hours}h {$minutes}m",
        ];
    }
}
