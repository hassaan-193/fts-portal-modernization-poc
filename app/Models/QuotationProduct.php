<?php

namespace App\Models;

use Eloquent as Model;

class QuotationProduct extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
