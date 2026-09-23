<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;

/**
 * Class PaymentInvoice
 * @package App\Models
 * @version July 25, 2020, 2:14 pm PKT
 *
 * @property string $invoice_no
 * @property integer $lpoout_id
 * @property integer $invoice_request_id
 * @property string $start_date
 * @property string $end_date
 * @property number $amount
 * @property string $note
 * @property integer $status
 */
class PaymentInvoice extends Model
{
    use DeleteRecord;

    public $table = 'payment_invoices';

    public $fillable = [
        'type',
        'invoice_no',
        'lpoout_id',
        'invoice_request_id',
        'vendor_id',
        'project_id',
        'start_date',
        'end_date',
        'amount',
        'vat',
        'total_amount',
        'note',
        'status',
        // Historical payment fields
        'is_historical',
        'source_reference',
        'source_date',
        'historical_party',
        'historical_project',
        'created_by',
        'document_path',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_no' => 'string',
        'lpoout_id' => 'integer',
        'invoice_request_id' => 'integer',
        'amount' => 'double',
        'note' => 'string',
        'status' => 'integer',
        'is_historical' => 'boolean',
        'source_date' => 'date',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'lpoout_id' => 'required_if:type,==,LpoOut',
        'amount' => 'required|numeric'
    ];

    public function lpoout()
    {
        return $this->belongsTo(\App\Models\Lpoout::class);
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    public function request()
    {
        return $this->belongsTo(\App\Models\InvoiceRequest::class,'invoice_request_id','id');
    }

    public function transaction()
    {
        return $this->morphOne(Receipt::class, 'transactionable');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
