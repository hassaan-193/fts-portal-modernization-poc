<?php

namespace App\Models;

use Eloquent as Model;

class InquiryFollowUp extends Model
{
    public $table = 'inquiry_follow_ups';

    const STATUSES = ['Under Follow-up', 'Won', 'Lost'];

    public $fillable = [
        'inquiry_id',
        'follow_up_date',
        'follow_up_notes',
        'client_feedback',
        'status',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public static $rules = [
        'follow_up_date'  => 'required|date',
        'follow_up_notes' => 'required|string',
        'client_feedback' => 'nullable|string',
        'status'          => 'required|in:Under Follow-up,Won,Lost',
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
