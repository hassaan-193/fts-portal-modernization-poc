<?php

namespace App\Traits;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use Carbon\Carbon;

trait AttendanceHelper
{
    /**
     * Get attendance status for a date range
     */
    public static function getAttendanceStats($startDate, $endDate, $staffId = null)
    {
        $query = AttendanceSession::completedSessions()
            ->whereBetween('session_date', [$startDate, $endDate]);

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $sessions = $query->get();

        return [
            'total_days_worked' => $sessions->groupBy('session_date')->count(),
            'total_sessions' => $sessions->count(),
            'total_late_sessions' => $sessions->where('is_late', true)->count(),
            'total_hours' => round($sessions->sum('duration_minutes') / 60, 2),
        ];
    }

    /**
     * Get late details for a staff member
     */
    public static function getLateDetails($staffId, $startDate, $endDate)
    {
        $lateSessions = AttendanceSession::where('staff_id', $staffId)
            ->where('is_late', true)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->orderBy('session_date', 'desc')
            ->get();

        return $lateSessions->map(function ($session) {
            return [
                'date' => $session->session_date->format('Y-m-d'),
                'clock_in' => $session->clock_in_time->format('H:i:s'),
                'shift' => $session->shift_window,
            ];
        });
    }

    /**
     * Format hours to HH:MM format
     */
    public static function formatHours($minutes)
    {
        if (!$minutes) {
            return '00:00';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Check if employee can clock out (has open session)
     */
    public static function canClockOut($staffId)
    {
        return AttendanceSession::forStaff($staffId)
            ->onDate(Carbon::today())
            ->openSessions()
            ->exists();
    }

    /**
     * Check if employee has clocked in today
     */
    public static function hasClockedInToday($staffId)
    {
        return AttendanceSession::forStaff($staffId)
            ->onDate(Carbon::today())
            ->exists();
    }
}
