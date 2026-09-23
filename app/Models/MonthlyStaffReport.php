<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyStaffReport extends Model
{

    protected $fillable = [
        'staff_id',
        'month',
        'year',
        'safety_avg',
        'communication_avg',
        'attendance_avg',
        'time_management_avg',
        'job_responsibility_avg',
        'material_handling_avg',
        'document_handling_avg',
        'competency_avg',
        'final_score',
    ];

    public function staff()
    {
        return $this->belongsTo(StafProfile::class, 'staff_id');
    }
}
