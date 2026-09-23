<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class InvoiceProductDetail
 * @package App\Models
 * @version July 6, 2020, 2:39 pm PKT
 *
 * @property integer invoice_id
 * @property string product
 * @property string unit
 * @property integer qty
 * @property number rate
 * @property number amount
 * @property number vat
 * @property number total_amount
 */
class InvoiceProductDetail extends Model
{

    public $table = 'invoice_product_details';

    public $fillable = [
        'invoice_id',
        'product',
        'unit',
        'qty',
        'rate',
        'amount',
        'vat',
        'total_amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_id' => 'integer',
        'product' => 'string',
        'unit' => 'string',
        'qty' => 'float',
        'rate' => 'double',
        'amount' => 'double',
        'vat' => 'double',
        'total_amount' => 'double'
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class);
    }

}
