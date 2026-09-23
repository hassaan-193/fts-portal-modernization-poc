<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;
use App\Traits\Models\InvoiceRequestTrait;

/**
 * Class InvoiceRequest
 * @package App\Models
 * @version July 11, 2020, 1:11 am PKT
 *
 * @property integer user_id
 * @property integer quotation_id
 * @property string note
 * @property integer status
 */
class InvoiceRequest extends Model
{
    use InvoiceRequestTrait;

    public $table = 'invoice_requests';

    public $fillable = [
        'user_id',
        'requestable_id',
        'requestable_type',
        'note',
        'payment_terms',
        'delivery_date',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'note' => 'string',
        'status' => 'integer'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getRequestTime()
    {
        return $this->created_at;
        // return $this->created_at->diffForHumans();;
    }

    public function requestable()
    {
        return $this->morphTo();
    }

    public function request_products()
    {
        return $this->hasMany(\App\Models\InvoiceRequestProduct::class, 'request_id');
    }
}
