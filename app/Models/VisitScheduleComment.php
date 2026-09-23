<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitScheduleComment extends Model
{

    protected $fillable = [
        'visit_schedule_id', 
        'comment',
    ];
    public function visitSchedule()
    {
        return $this->belongsTo(VisitSchedule::class);
    }
}
