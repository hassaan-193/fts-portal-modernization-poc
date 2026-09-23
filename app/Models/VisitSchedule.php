<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitSchedule extends Model
{
    protected $table = 'visit_schedules'; // Table name

    protected $fillable = [
        'project_id',
        'visit_date',
        'status',
        'file_uploaded',
        'file_path',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function comments()
    {
        return $this->hasMany(VisitScheduleComment::class);
    }
    public function visitScheduleHistory()
    {
        return $this->hasMany(VisitScheduleHistory::class);
    }
    public function projectReport()
    {
        return $this->hasOne(ProjectReport::class, 'visit_schedule_id');
    }


}
