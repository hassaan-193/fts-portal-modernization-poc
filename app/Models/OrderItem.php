<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'item_description', 'unit', 'quantity', 'unit_price',
        'total', 'discount', 'total_after_discount', 'vat', 'total_with_vat'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
