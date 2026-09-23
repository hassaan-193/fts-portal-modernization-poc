<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLaborDetail extends Model
{
    protected $fillable = [
        'attendance_id', 'labor_id', 'overtime_hours',
        'site_id', 'custom_site_name'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function labor()
    {
        return $this->belongsTo(StafProfile::class, 'labor_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}