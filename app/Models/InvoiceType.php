<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class InvoiceType
 * @package App\Models
 * @version July 2, 2020, 5:27 pm PKT
 *
 * @property string name
 */
class InvoiceType extends Model
{

    public $table = 'invoice_types';

    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
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
