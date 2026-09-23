<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class StaffRating extends Model
{

    protected $fillable = [
        'staff_id',
        'engineer_id',
        'month',
        'year',
        'safety_compliance',
        'communication',
        'attendance',
        'time_management',
        'job_responsibility',
        'material_handling',
        'document_handling',
        'competency',
    ];

    public function staff()
    {
        return $this->belongsTo(StafProfile::class, 'staff_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }
}
