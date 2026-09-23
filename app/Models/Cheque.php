<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;

/**
 * Class Cheque
 * @package App\Models
 * @version August 6, 2020, 5:03 pm PKT
 *
 * @property integer $account_id
 */
class Cheque extends Model
{
    use DeleteRecord;

    public $table = 'transactions';
    public $fillable = [
        'account_id',
        'status'
    ];
    public static $rules = [
        'account_id' => 'required'
    ];

    public function transaction_payment_type()
    {
        return $this->belongsTo(\App\Models\Lookup::class, 'payment_type', 'id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id', 'id');
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function company()
    {
        return $this->hasManyDeep(
            \App\Models\Invoice::class,
            [\App\Models\Quotation::class],
            ['id', 'id', 'quotation_id'],
            ['transactionable_id','quotation_id','id']
        )
        ->where('transactionable_type', \App\Models\Invoice::class);;
    }
}
