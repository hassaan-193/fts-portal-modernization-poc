<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UplodeFileStafProfile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Carbon\Carbon;

class StafProfile extends Model implements HasMedia
{
   //Use File Upload Traits
   use HasMediaTrait;
   use UplodeFileStafProfile;
   use DeleteRecord;

   public $table = 'staf_profile';
   public $fillable = [
    'name',
    'staf_type',
    'last_name',
    'nationality',
    'gender',
    'joining_date',
    'dob',
    'passport_expiry',
    'visa_expiry',
    'emirates_id_expiry',
    'labor_card_expiry',
    'driver_permit_expiry',
    'last_vacation_start',
    'last_vacation_days',
    'last_vacation_end',
    'last_increment_amount',
    'last_increment',
    'basic_salary',
    'total_salary',
    'overtime_rate',
    'mobile_no',
    'home_mobile_no',
    'exclude_from_expiry',
    'work_mode',

];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'last_name' => 'required',
        'joining_date' => 'required',
        'last_vacation_days' => 'nullable',
        'basic_salary' => 'sometimes|numeric',
        'total_salary' => 'sometimes|numeric',
        'overtime_rate' => 'sometimes|numeric',
        'mobile_no' => 'required|regex:/^\+?[1-9]\d{1,14}$/',  // Validate international phone number format
    ];

    public function payroll()
    {
        return $this->hasMany(StaffPayroll::class, 'member_id');
    }

    public function labor_requests()
    {
        return $this->hasMany(LaborRequest::class, 'labor_id');
    }

    public function leaves_available()
    {
        return $this->hasMany(StafDates::class, 'staff_id');
    }

    public function remainingLeaveDays()
    {
        // Current year
        $currentYear = Carbon::now()->year;

        // Filter leaves for the current year
        $leaves = $this->leaves_available()
            ->whereYear('start_date', $currentYear)
            ->whereYear('end_date', $currentYear)
            ->get();

        // Calculate total days taken
        $totalDaysTaken = $leaves->reduce(function ($carry, $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            return $carry + $startDate->diffInDays($endDate);
        }, 0);

        // Subtract 26 (total allocated days)
        $remainingDays = max(config('enum.total_staff_leaves') - $totalDaysTaken, 0);

        return $remainingDays;
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'staf_id');
    }

    public function letters()
    {
        return $this->hasMany(Letter::class,'staff_profile_id');
    }
    
    public function ratings()
    {
        return $this->hasMany(\App\Models\StaffRating::class, 'staff_id');
    }


        /**
     * Get all attendance records for this labor
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'labor_id');
    }

    /**
     * Get today's attendance record
     */
    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()
            ->whereDate('attendance_date', today())
            ->first();
    }

    /**
     * Check if labor is marked absent today
     */
    public function isAbsentToday()
    {
        return $this->attendances()
            ->whereDate('attendance_date', today())
            ->exists();
    }

    public function approvedAbsences()
    {
        return $this->hasMany(Attendance::class, 'labor_id')
            ->where('status', 'approved')
            ->orderBy('attendance_date', 'desc');
    }

    /**
     * Get all attendance sessions for this staff member
     */
    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class, 'staff_id');
    }

}
