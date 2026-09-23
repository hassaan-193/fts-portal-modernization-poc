<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;

/**
 * Class Employee
 * @package App\Models
 * @version November 23, 2020, 10:48 am PKT
 *
 * @property string $name
 * @property string $email
 * @property string $code
 * @property string $contact_no
 * @property number $balance
 */
class Employee extends Model
{
    use DeleteRecord;

    public $table = 'employees';




    public $fillable = [
        'name',
        'email',
        'code',
        'contact_no',
        'balance'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'contact_no' => 'string',
        'balance' => 'double'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];


}
