<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;

/**
 * Class Account
 * @package App\Models
 * @version August 4, 2020, 9:28 pm PKT
 *
 * @property string $name
 * @property number $balance
 */
class Account extends Model
{
    use DeleteRecord;

    public $table = 'balance_accounts';

    public $timestamps = false;

    public $fillable = [
        'type',
        'balance',
        'user_id',
        'code',
        'account_type'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'type' => 'required',
        'account_type' => 'required',
    ];
}
