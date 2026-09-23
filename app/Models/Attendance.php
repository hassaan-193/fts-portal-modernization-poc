<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'labor_id',
        'foreman_id',
        'approved_by',
        'site_id',                
        'custom_site_name',       
        'overtime_hours',         
        'attendance_date',
        'status',
        'notes',
        'marked_at',
        'approved_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'marked_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ============================================
    // CONSTANTS
    // ============================================

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PRESENT = 'present'; 

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the labor associated with the attendance
     */
    public function labor()
    {
        return $this->belongsTo(StafProfile::class, 'labor_id');
    }

    /**
     * Get the foreman who marked the attendance
     */
    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    /**
     * Get the manager who approved the attendance
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the site associated with the attendance
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    /**
     * Get all approval records for this attendance
     */
    public function approvals()
    {
        return $this->hasMany(AttendanceApproval::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope to get pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get approved records
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get rejected records
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope to get records by site
     */
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope to filter by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Scope to filter by foreman
     */
    public function scopeByForeman($query, $foremanId)
    {
        return $query->where('foreman_id', $foremanId);
    }

    /**
     * Scope to filter by labor
     */
    public function scopeByLabor($query, $laborId)
    {
        return $query->where('labor_id', $laborId);
    }

    // ============================================
    // METHODS
    // ============================================

    /**
     * Check if attendance is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if attendance is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if attendance is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if attendance is for a present labor
     */
    public function isPresent()
    {
        return $this->status === self::STATUS_PRESENT;
    }

    /**
     * Check if the attendance record has overtime
     */
    public function hasOvertime()
    {
        return $this->overtime_hours > 0;
    }
    /**
     * Get all labor details associated with this attendance
     */
    public function laborDetails()
    {
        return $this->hasMany(AttendanceLaborDetail::class);
    }

    public function latestApproval()
    {
        return $this->hasOne(AttendanceApproval::class)->latest();
    }
}