<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Carbon;

class LaborRequest extends Model
{
    public $table = 'labor_requests';

    public $fillable = [
        'labor_id',
        'type',
        'start_date',
        'end_date',
        'advance_money',
        'note',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'labor_id' => 'integer',
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

    public function labor()
    {
        return $this->belongsTo(StafProfile::class, 'labor_id');
    }
}

