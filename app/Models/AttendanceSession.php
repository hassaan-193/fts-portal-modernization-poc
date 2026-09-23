<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;


class AttendanceSession extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'user_id',
        'session_date',
        'clock_in_time',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_in_distance_meters',
        'clock_out_time',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_out_distance_meters',
        'duration_minutes',
        'shift_window',
        'is_late',
        'work_mode',
        'notes',
        'session_status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'clock_in_latitude' => 'float',
        'clock_in_longitude' => 'float',
        'clock_out_latitude' => 'float',
        'clock_out_longitude' => 'float',
        'is_late' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // ============================================
    // CONSTANTS
    // ============================================

    const SHIFT_1 = 'shift_1';
    const SHIFT_2 = 'shift_2';

    const WORK_MODE_SPLIT_SHIFT = 'split_shift';
    const WORK_MODE_CONTINUOUS = 'continuous';

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the user associated with this session
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to get sessions for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get sessions on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('session_date', $date);
    }

    /**
     * Scope to get only open sessions (clock-in without clock-out)
     */
    public function scopeOpenSessions($query)
    {
        return $query->where('session_status', 'open');
    }

    /**
     * Scope to get only closed sessions (both clock-in and clock-out)
     */
    public function scopeClosedSessions($query)
    {
        return $query->where('session_status', 'closed');
    }

    /**
     * Scope to get late sessions
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope to get sessions for a specific shift
     */
    public function scopeForShift($query, $shiftWindow)
    {
        return $query->where('shift_window', $shiftWindow);
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Calculate session duration in minutes
     * Called after clock_out_time is set
     */
    public function calculateDuration()
    {
        if ($this->clock_in_time && $this->clock_out_time) {
            $clockIn = Carbon::parse($this->clock_in_time);
            $clockOut = Carbon::parse($this->clock_out_time);
            $this->duration_minutes = (int) $clockIn->diffInMinutes($clockOut);
        }
        return $this;
    }

    /**
     * Check if session is open (clock-in without clock-out)
     */
    public function isOpen()
    {
        return $this->clock_out_time === null;
    }

    /**
     * Check if session is completed (both clock-in and clock-out)
     */
    public function isCompleted()
    {
        return $this->clock_out_time !== null;
    }

    /**
     * Check if clock-in is within allowed radius
     */
    public function isClockInWithinRadius()
    {
        return $this->clock_in_distance_meters !== null && 
               $this->clock_in_distance_meters >= 0; // Calculated by service
    }

    /**
     * Check if clock-out is within allowed radius
     */
    public function isClockOutWithinRadius()
    {
        return $this->clock_out_distance_meters !== null && 
               $this->clock_out_distance_meters >= 0;
    }
}
