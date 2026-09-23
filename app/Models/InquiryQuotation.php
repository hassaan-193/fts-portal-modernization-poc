<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class InquiryQuotation extends Model implements HasMedia
{
    use HasMediaTrait;

    public $table = 'inquiry_quotations';

    public $fillable = [
        'inquiry_id',
        'quotation_amount',
        'scope_of_work',
        'terms_conditions',
        'validity_date',
        'created_by',
    ];

    protected $casts = [
        'quotation_amount' => 'decimal:2',
        'validity_date'    => 'date',
    ];

    public static $rules = [
        'quotation_amount' => 'required|numeric|min:0',
        'scope_of_work'    => 'nullable|string',
        'terms_conditions' => 'nullable|string',
        'validity_date'    => 'nullable|date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
