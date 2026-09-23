<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Carbon;

class StaffRequest extends Model
{
    public $table = 'staff_requests';

    public $fillable = [
        'staf_id',
        'type',
        'start_date',
        'end_date',
        'advance_money',
        'note',
        'status',
        'device_type',
        'letter_type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'staf_id' => 'integer',
        'type' => 'string',
        'advance_money' => 'double',
        'note' => 'string',
        'created_at' => 'date',
    ];


    // Define accessor for custom date format
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s'); // Customize format here
    }

    public function staf()
    {
        return $this->belongsTo(StafProfile::class, 'staf_id');
    }
}

