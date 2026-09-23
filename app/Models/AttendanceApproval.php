<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;  // CHANGE THIS LINE

class AttendanceApproval extends Model
{

    protected $table = 'attendance_approvals';

    protected $fillable = [
        'attendance_id',
        'approved_by',
        'action',
        'reason',
        'informed',
        'specific_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the attendance record
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance:: class);
    }

    /**
     * Get the user who approved
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}