<?php

namespace App\Models;

use Eloquent as Model;

class InquiryActivity extends Model
{
    public $table = 'inquiry_activities';

    // Action constants for consistency
    const ACTION_CREATED         = 'inquiry_created';
    const ACTION_STATUS_CHANGED  = 'status_changed';
    const ACTION_ASSIGNED        = 'assigned';
    const ACTION_DEPARTMENT_REVIEW = 'department_review_submitted';
    const ACTION_SITE_VISIT_SCHEDULED = 'site_visit_scheduled';
    const ACTION_ENGINEER_REPORT = 'engineer_report_submitted';
    const ACTION_SENT_TO_SALES   = 'sent_to_sales';
    const ACTION_QUOTATION_CREATED = 'quotation_created';
    const ACTION_FOLLOW_UP_ADDED = 'follow_up_added';
    const ACTION_FILE_UPLOADED   = 'file_uploaded';
    const ACTION_NOTE_ADDED      = 'note_added';

    public $fillable = [
        'inquiry_id',
        'user_id',
        'action',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
