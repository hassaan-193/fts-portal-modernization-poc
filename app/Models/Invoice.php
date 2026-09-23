<?php

namespace App\Models;

use Eloquent as Model;
// use App\Traits\UploadFileInvoice;
use App\Traits\DeleteRecord;
use App\Traits\Models\InvoiceTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Carbon\Carbon;

/**
 * Class Invoice
 * @package App\Models
 * @version July 5, 2020, 3:24 pm PKT
 *
 * @property string invoice_no
 * @property integer invoice_type_id
 * @property integer quotation_id
 * @property string start_date
 * @property string end_date
 * @property string delivery_date
 * @property string payment_terms
 * @property string amount_in_word
 * @property string note1
 * @property string note2
 * @property integer status
 */
class Invoice extends Model implements HasMedia
{
    use HasMediaTrait,InvoiceTrait;
    // use CacheQueryBuilder;
    use DeleteRecord;

    public $table = 'invoices';

    public $fillable = [
        'invoice_no',
        'invoice_type_id',
        'quotation_id',
        'invoice_request_id',
        'start_date',
        'end_date',
        'currency',
        'delivery_date',
        'payment_terms',
        'amount_in_word',
        'note1',
        'note2',
        'invoice_bank_id',
        'amount',
        'vat',
        'total_amount',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_no' => 'string',
        'invoice_type_id' => 'integer',
        'quotation_id' => 'integer',
        'payment_terms' => 'string',
        'amount_in_word' => 'string',
        'note1' => 'string',
        'note2' => 'string',
        'status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'invoice_no' => 'required|unique:invoices',
        'invoice_type_id' => 'required',
        'quotation_id' => 'required',
        "amount"  => "min:1",
        "product.*"  => "required",
        "service_amount.*"  => "required|numeric"
    ];

    public static $updaterules = [
        'invoice_no' => 'required',
        'invoice_type_id' => 'required',
        'quotation_id' => 'required',
        "amount"  => "min:1",
        "product.*"  => "required",
        "service_amount.*"  => "required|numeric"
    ];

    protected $with = ['quotation.company'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function invoice_type()
    {
        return $this->belongsTo(\App\Models\InvoiceType::class,'invoice_type_id','id');
    }

    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class);
    }

    public function invoice_product_details()
    {
        return $this->hasMany(\App\Models\InvoiceProductDetail::class);
    }

    public function invoice_service_details()
    {
        return $this->hasMany(\App\Models\InvoiceServiceDetail::class);
    }

    public function invoice_bank()
    {
        return $this->belongsTo(\App\Models\InvoiceBank::class,'invoice_bank_id','id');
    }

    public function request()
    {
        return $this->belongsTo(\App\Models\InvoiceRequest::class,'invoice_request_id','id');
    }

    public function transaction()
    {
        return $this->morphOne(Receipt::class, 'transactionable');
    }

    public function journal_entry()
    {
        return $this->morphOne(Transaction::class, 'reference');
    }
}
