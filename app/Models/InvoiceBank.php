<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class InvoiceBank
 * @package App\Models
 * @version July 20, 2020, 2:54 pm PKT
 *
 * @property string beneficary_account_name
 * @property string bank_name
 * @property string bank_branch
 * @property string account_no
 * @property string account_currency
 * @property string iban_no
 * @property string swift_code
 */
class InvoiceBank extends Model
{

    public $table = 'invoice_banks';
    



    public $fillable = [
        'beneficary_account_name',
        'bank_name',
        'bank_branch',
        'account_no',
        'account_currency',
        'iban_no',
        'swift_code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'beneficary_account_name' => 'string',
        'bank_name' => 'string',
        'bank_branch' => 'string',
        'account_no' => 'string',
        'account_currency' => 'string',
        'iban_no' => 'string',
        'swift_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'beneficary_account_name' => 'required',
        'bank_name' => 'required',
        'account_no' => 'required',
        'account_currency' => 'required'
    ];

    
}
