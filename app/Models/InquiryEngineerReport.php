<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class InquiryEngineerReport extends Model implements HasMedia
{
    use HasMediaTrait;

    public $table = 'inquiry_engineer_reports';

    public $fillable = [
        'inquiry_id',
        'visit_completed',
        'site_condition_notes',
        'scope_understanding',
        'materials_required',
        'challenges_risks',
        'estimated_cost',
        'submitted_by',
    ];

    protected $casts = [
        'visit_completed' => 'boolean',
        'estimated_cost'  => 'decimal:2',
    ];

    public static $rules = [
        'visit_completed'      => 'required|boolean',
        'site_condition_notes' => 'nullable|string',
        'scope_understanding'  => 'nullable|string',
        'materials_required'   => 'nullable|string',
        'challenges_risks'     => 'nullable|string',
        'estimated_cost'       => 'nullable|numeric|min:0',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function engineer()
    {
        return $this->belongsTo(\App\User::class, 'submitted_by');
    }
}
