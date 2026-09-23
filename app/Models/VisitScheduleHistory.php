<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitScheduleHistory extends Model
{
    protected $table = 'visit_schedule_history';

    protected $fillable = [
        'project_id',
        'visit_schedule_id',
        'visit_date',
        'status',
        'company_name',
        'project_name',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function visitSchedule()
    {
        return $this->belongsTo(VisitSchedule::class);
    }
}
