<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;
use App\Traits\UploadFile;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class PurchaseOrder extends Model implements HasMedia
{
    use DeleteRecord, HasMediaTrait, UploadFile;

    public $table = 'purchase_orders';

    protected $with = [];  // Don't eager load by default to avoid errors if table doesn't exist

    public $fillable = [
        'request_number',
        'request_type',
        'quotation_id',
        'project_id',
        'other_info',
        'created_by',
        'date',
        'due_date',
        'delivery_date',
        'items',
        'total_amount',
        'status',
        'department_status',
        'department_notes',
        'admin_id',
        'admin_notes',
        'lpout_id',
        'revised_from_lpoout_id',
        'lpout_name',
        'lpout_vendor_id',
        'lpout_trn_no',
        'lpout_kindly_attn',
        'lpout_date',
        'lpout_payment_type',
        'lpout_cheque_date',
        'lpout_vat',
        'lpout_payment_preference_option',
        'lpout_payment_preference',
        'lpout_pdc_number_of_days',
        'lpout_pdc_payment_option',
        'lpout_items',
        'lpout_pricing_mode',
        'lpout_manual_total',
        'has_item_code',
        'lpout_terms',
        'payment_preference',
        'urgency_level',
        'sent_back_notes',
        'sent_back_at',
        'sent_back_by',
        'sent_back_count'
    ];

    protected $casts = [
        'id' => 'integer',
        'quotation_id' => 'integer',
        'project_id' => 'integer',
        'created_by' => 'integer',
        'admin_id' => 'integer',
        'lpout_id' => 'integer',
        'revised_from_lpoout_id' => 'integer',
        'lpout_vendor_id' => 'integer',
        'lpout_vat' => 'integer',
        'lpout_pdc_number_of_days' => 'integer',
        'items' => 'array',
        'lpout_items' => 'array',
        'has_item_code' => 'boolean',
        'sent_back_at' => 'datetime',
        'sent_back_by' => 'integer',
        'sent_back_count' => 'integer',
        'lpout_manual_total' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
        'delivery_date' => 'date',
        'lpout_date' => 'date',
        'lpout_cheque_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    /**
     * Lump-sum pricing: one batch total instead of per-row costs. The cost
     * columns are then irrelevant and are hidden on every screen and print.
     */
    public function isLumpSum(): bool
    {
        return ($this->lpout_pricing_mode ?? 'unit') === 'lump';
    }

    /**
     * Whether line items carry an item code (extra column before the material name).
     */
    public function hasItemCode(): bool
    {
        return (bool) $this->has_item_code;
    }

    /**
     * Sent back by the department to Step 1 and not yet resubmitted.
     */
    public function isSentBack(): bool
    {
        return $this->department_status === 'Sent Back';
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }

    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class, 'quotation_id', 'id')->withDefault();
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'lpout_vendor_id', 'id')->withDefault();
    }

    public function getCompanyName()
    {
        try {
            // For 'project' type: get company through project -> quotation -> company
            if ($this->request_type === 'project') {
                if ($this->project && $this->project->quotation && $this->project->quotation->company) {
                    return $this->project->quotation->company->name ?? 'N/A';
                }
            }
            
            // For 'maintenance' or other types: get company through quotation -> company
            if ($this->quotation && $this->quotation->company) {
                return $this->quotation->company->name ?? 'N/A';
            }
            
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id')->withDefault();
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\User::class, 'admin_id', 'id')->withDefault();
    }

    public function lpout()
    {
        return $this->belongsTo(\App\Models\Lpoout::class, 'lpout_id', 'id')->withDefault();
    }

    public function sentBackBy()
    {
        return $this->belongsTo(\App\User::class, 'sent_back_by', 'id')->withDefault();
    }

    public function revisedFromLpoout()
    {
        return $this->belongsTo(\App\Models\Lpoout::class, 'revised_from_lpoout_id', 'id');
    }
}
