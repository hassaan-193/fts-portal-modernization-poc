<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class InvoiceServiceDetail
 * @package App\Models
 * @version July 6, 2020, 4:22 pm PKT
 *
 * @property integer invoice_id
 * @property string description
 * @property number amount
 */
class InvoiceServiceDetail extends Model
{
    public $table = 'invoice_service_details';

    public $fillable = [
        'invoice_id',
        'description',
        'amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_id' => 'integer',
        'description' => 'string',
        'amount' => 'double'
    ];

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class);
    }
}
