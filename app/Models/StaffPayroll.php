<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class StaffPayroll
 * @package App\Models
 * @version November 14, 2022, 12:02 am PKT
 *
 * @property integer $payrollable_id
 * @property string $payrollable_type
 * @property number $hours
 * @property number $plus_adjustment
 * @property number $minus_adjustment
 * @property number $total_amount
 * @property string $note
 */
class StaffPayroll extends Model
{
    public $table = 'payrolls';

    public $fillable = [
        'date',
        'member_id',
        'absents',
        'hours',
        'plus_adjustment',
        'minus_adjustment',
        'total_amount',
        'note'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'payrollable_id' => 'integer',
        'payrollable_type' => 'string',
        'date' => 'datetime',
        'absents' => 'integer',
        'hours' => 'double',
        'plus_adjustment' => 'double',
        'minus_adjustment' => 'double',
        'total_amount' => 'double',
        'note' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'date' => 'required'
    ];
    /**
     * Get the profile that owns the StaffPayroll
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(StafProfile::class, 'member_id');
    }
}

