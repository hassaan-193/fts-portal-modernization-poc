<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StafDates extends Model
{
   public $table = 'staf_dates';
   public $fillable = [
    'staff_id',
    'start_date',
    'end_date',
    'days'
    ];
    
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'staff_id' => 'required',
        'start_date' => 'required',
    ];

    /**
     * Relationship: A staff date belongs to a staff member
     */
    public function staff()
    {
        return $this->belongsTo(StafProfile::class, 'staff_id');
    }
}
