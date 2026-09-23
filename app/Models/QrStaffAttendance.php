<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Carbon\Carbon;

class QrStaffAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'qr_staff_attendances';

    protected $fillable = [
        'staff_id',
        'scanned_by',
        'site_id',
        'custom_site_name',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'duration_minutes',
        'overtime_minutes',
        'shift_end_time',
        'status',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'final_overtime_source',
        'final_overtime_minutes',
        'finalized_by',
        'finalized_at',
        'final_decision_notes',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'reviewed_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const REVIEW_PENDING = 'pending';
    const REVIEW_APPROVED = 'approved';
    const REVIEW_REJECTED = 'rejected';
    const FINAL_SOURCE_MANUAL = 'manual';
    const FINAL_SOURCE_QR = 'qr';
    const FINAL_SOURCE_CUSTOM = 'custom';
    const DEFAULT_SHIFT_END = '17:00:00';
    const MIN_CHECKOUT_SECONDS = 60;

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function staff()
    {
        return $this->belongsTo(StafProfile::class, 'staff_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function finalizer()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN);
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', self::STATUS_CHECKED_OUT);
    }

    // ============================================
    // METHODS
    // ============================================

    public function isOpen()
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public function calculateDuration()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $this->duration_minutes = (int) Carbon::parse($this->check_in_time)
                ->diffInMinutes(Carbon::parse($this->check_out_time));
        }
        return $this;
    }

    public function getHoursWorked()
    {
        return $this->duration_minutes ? round($this->duration_minutes / 60, 2) : 0;
    }

    /**
     * Human-readable duration, e.g. 38m, 1h 30m, 8h.
     */
    public static function formatMinutes(?int $minutes): string
    {
        $minutes = max(0, (int) $minutes);

        if ($minutes === 0) {
            return '0h';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return $hours . 'h ' . $remainingMinutes . 'm';
        }

        if ($hours > 0) {
            return $hours . 'h';
        }

        return $remainingMinutes . 'm';
    }

    public static function formatDecimalHours($hours): string
    {
        return self::formatMinutes((int) round((float) $hours * 60));
    }

    public function calculateOvertime()
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            $this->overtime_minutes = 0;

            return $this;
        }

        // Overtime only counts after this labor's 8-hour shift (from actual check-in).
        // Late arrivals working past nominal shift end are not OT until 8 hours are completed.
        $checkIn = Carbon::parse($this->check_in_time);
        $shiftCompleteAt = $checkIn->copy()->addHours(8);
        $checkOut = Carbon::parse($this->check_out_time);

        if ($checkOut->lte($shiftCompleteAt)) {
            $this->overtime_minutes = 0;
        } else {
            $this->overtime_minutes = (int) $shiftCompleteAt->diffInMinutes($checkOut);
        }

        return $this;
    }

    public function canCheckOut()
    {
        if (!$this->isOpen()) {
            return false;
        }
        return Carbon::parse($this->check_in_time)
            ->diffInSeconds(now()) >= self::MIN_CHECKOUT_SECONDS;
    }
}
