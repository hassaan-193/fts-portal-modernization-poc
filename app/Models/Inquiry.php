<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Inquiry extends Model implements HasMedia
{
    use HasMediaTrait;

    public $table = 'inquiries';

    // Inquiry lifecycle statuses
    const STATUS_NEW                = 'New';
    const STATUS_ASSIGNED           = 'Assigned';
    const STATUS_UNDER_REVIEW       = 'Under Review';
    const STATUS_SITE_VISIT_PENDING = 'Site Visit Pending';
    const STATUS_SITE_VISIT_DONE    = 'Site Visit Done';
    const STATUS_SENT_TO_SALES      = 'Sent to Sales';
    const STATUS_QUOTATION_CREATED  = 'Quotation Created';
    const STATUS_UNDER_FOLLOW_UP    = 'Under Follow-up';
    const STATUS_WON                = 'Won';
    const STATUS_LOST               = 'Lost';
    const STATUS_CLOSED             = 'Closed';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_ASSIGNED,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_SITE_VISIT_PENDING,
        self::STATUS_SITE_VISIT_DONE,
        self::STATUS_SENT_TO_SALES,
        self::STATUS_QUOTATION_CREATED,
        self::STATUS_UNDER_FOLLOW_UP,
        self::STATUS_WON,
        self::STATUS_LOST,
        self::STATUS_CLOSED,
    ];

    const INQUIRY_TYPES   = ['Project', 'AMC', 'Installation', 'Other'];
    const SOURCES         = ['Call', 'Email', 'Walk-in', 'Website', 'Referral'];
    const PRIORITIES      = ['Low', 'Medium', 'High'];

    /**
     * Valid workflow transitions per status.
     * Admin bypasses these; other roles must follow the chain.
     */
    const TRANSITIONS = [
        self::STATUS_NEW                => [self::STATUS_ASSIGNED],
        self::STATUS_ASSIGNED           => [self::STATUS_UNDER_REVIEW],
        self::STATUS_UNDER_REVIEW       => [self::STATUS_SITE_VISIT_PENDING, self::STATUS_SENT_TO_SALES],
        self::STATUS_SITE_VISIT_PENDING => [self::STATUS_SITE_VISIT_DONE],
        self::STATUS_SITE_VISIT_DONE    => [self::STATUS_SENT_TO_SALES],
        self::STATUS_SENT_TO_SALES      => [self::STATUS_QUOTATION_CREATED],
        self::STATUS_QUOTATION_CREATED  => [self::STATUS_UNDER_FOLLOW_UP],
        self::STATUS_UNDER_FOLLOW_UP    => [self::STATUS_WON, self::STATUS_LOST],
        self::STATUS_WON                => [self::STATUS_CLOSED],
        self::STATUS_LOST               => [self::STATUS_CLOSED],
        self::STATUS_CLOSED             => [],
    ];

    public $fillable = [
        'inquiry_no',
        'created_by',
        'client_name',
        'phone',
        'email',
        'location',
        'project',
        'inquiry_type',
        'other_type',
        'source',
        'expected_price',
        'status',
        'priority',
        'follow_up_date',
        'expected_closing_date',
        'assigned_department',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata'              => 'array',
        'follow_up_date'        => 'date',
        'expected_closing_date' => 'date',
        'expected_price'        => 'decimal:2',
    ];

    public static $rules = [
        'client_name'   => 'required|string|max:255',
        'phone'         => 'required|string|max:50',
        'email'         => 'nullable|email|max:255',
        'location'      => 'nullable|string|max:255',
        'inquiry_type'  => 'required|string',
        'other_type'    => 'nullable|required_if:inquiry_type,Other|string|max:255',
        'source'        => 'required|string',
        'expected_price'=> 'nullable|numeric|min:0',
        'priority'      => 'required|in:Low,Medium,High',
        'follow_up_date'        => 'nullable|date',
        'expected_closing_date' => 'nullable|date',
        'notes'         => 'nullable|string',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function departmentReview()
    {
        return $this->hasOne(InquiryDepartmentReview::class, 'inquiry_id')->latest();
    }

    public function engineerReport()
    {
        return $this->hasOne(InquiryEngineerReport::class, 'inquiry_id')->latest();
    }

    public function quotation()
    {
        return $this->hasOne(InquiryQuotation::class, 'inquiry_id')->latest();
    }

    public function followUps()
    {
        return $this->hasMany(InquiryFollowUp::class, 'inquiry_id')->orderByDesc('follow_up_date');
    }

    public function activities()
    {
        return $this->hasMany(InquiryActivity::class, 'inquiry_id')->orderByDesc('created_at');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Generate the next sequential inquiry number in the format INQ-YYYY-NNNN.
     */
    public static function generateInquiryNo(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;

        return 'INQ-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a transition to the given status is valid for the current status.
     */
    public function canTransitionTo(string $newStatus, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            return true;
        }

        return in_array($newStatus, self::TRANSITIONS[$this->status] ?? []);
    }

    /**
     * Status badge colour for display purposes.
     */
    public function statusBadgeClass(): string
    {
        return [
            self::STATUS_NEW                => 'secondary',
            self::STATUS_ASSIGNED           => 'info',
            self::STATUS_UNDER_REVIEW       => 'primary',
            self::STATUS_SITE_VISIT_PENDING => 'warning',
            self::STATUS_SITE_VISIT_DONE    => 'info',
            self::STATUS_SENT_TO_SALES      => 'primary',
            self::STATUS_QUOTATION_CREATED  => 'info',
            self::STATUS_UNDER_FOLLOW_UP    => 'warning',
            self::STATUS_WON                => 'success',
            self::STATUS_LOST               => 'danger',
            self::STATUS_CLOSED             => 'dark',
        ][$this->status] ?? 'secondary';
    }
}
