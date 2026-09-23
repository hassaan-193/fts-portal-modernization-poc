<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'date', 'trn', 'vendor_id', 'attn', 'ship_to', 'address', 'contact', 'ref_no',
        'total_amount',
        'discount',
        'total_after_discount',
        'vat',
        'total_with_vat',
    ];
        protected $casts = [
            'date' => 'date',
        ];
        protected function serializeDate(\DateTimeInterface $date): string
        {
            return $date->format('Y-m-d');
        }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
