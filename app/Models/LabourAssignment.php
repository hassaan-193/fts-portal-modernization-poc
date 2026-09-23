<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabourAssignment extends Model
{

    protected $fillable = [
        'labor_id',
        'project_id',
        'visit_schedule_id',
        'assignment_start_date',
        'assignment_end_date',
        'hours_worked',
        'overtime_hours',
    ];

    public function labor()
    {
        return $this->belongsTo(StafProfile::class, 'labor_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function visitSchedule()
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_schedule_id');
    }
}
