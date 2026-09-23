<?php

namespace App\Models;

use Eloquent as Model;

class InquiryDepartmentReview extends Model
{
    public $table = 'inquiry_department_reviews';

    public $fillable = [
        'inquiry_id',
        'assigned_department',
        'assigned_to',
        'assignment_date',
        'current_status',
        'internal_comments',
        'priority',
        'response_deadline',
        'technical_review_status',
        'site_visit_required',
        'proposed_visit_date',
        'visit_assigned_to',
        'visit_notes',
        'reviewed_by',
    ];

    protected $casts = [
        'site_visit_required' => 'boolean',
        'assignment_date'     => 'date',
        'response_deadline'   => 'date',
        'proposed_visit_date' => 'date',
    ];

    public static $rules = [
        'assigned_department'     => 'required|string|max:255',
        'assigned_to'             => 'nullable|exists:users,id',
        'assignment_date'         => 'required|date',
        'priority'                => 'nullable|in:Low,Medium,High',
        'response_deadline'       => 'nullable|date',
        'technical_review_status' => 'nullable|string|max:255',
        'site_visit_required'     => 'boolean',
        'proposed_visit_date'     => 'nullable|required_if:site_visit_required,1|date',
        'visit_assigned_to'       => 'nullable|required_if:site_visit_required,1|exists:users,id',
        'visit_notes'             => 'nullable|string',
        'internal_comments'       => 'nullable|string',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }

    public function visitEngineer()
    {
        return $this->belongsTo(\App\User::class, 'visit_assigned_to');
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\User::class, 'reviewed_by');
    }
}
