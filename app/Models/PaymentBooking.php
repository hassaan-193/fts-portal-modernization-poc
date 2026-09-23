<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A cheque or cash payment booked by an accountant.
 *
 * The module is intentionally standalone: payee, project/cost centre, bank account
 * and cash account are free text rather than foreign keys, so nothing here depends
 * on the older finance tables.
 *
 * Review is a sequential two-level chain — a booking must clear level 1 (verify)
 * before level 2 (approve) can act on it. See App\Services\PaymentBookingService.
 *
 * "Released" is derived, never stored: an approved booking counts as released once
 * its effective date (release_date for cheques, payment_date for cash) has arrived.
 * That is what feeds the released-this-month / next-month figures on the dashboard.
 */
class PaymentBooking extends Model
{
    use SoftDeletes;

    const TYPE_CHEQUE = 'cheque';
    const TYPE_CASH   = 'cash';

    const STATUS_DRAFT    = 0;  // saved but not submitted for review
    const STATUS_PENDING  = 1;  // submitted, waiting on level 1
    const STATUS_VERIFIED = 2;  // level 1 cleared, waiting on level 2
    const STATUS_APPROVED = 3;  // fully approved
    const STATUS_REJECTED = 4;  // rejected at either level
    const STATUS_ON_HOLD  = 5;  // parked at either level, can be resumed

    public $table = 'payment_bookings';

    protected $dates = ['deleted_at', 'submitted_at', 'verified_at', 'approved_at', 'rejected_at'];

    public $fillable = [
        'reference_no',
        'booking_type',
        'payee',
        'payment_against',
        'purpose',
        'amount',
        'project_cost_centre',
        'booking_date',
        'cheque_number',
        'cheque_date',
        'bank_account',
        'release_date',
        'cash_account',
        'payment_date',
        'status',
        'created_by',
        'submitted_at',
        'verified_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'id'                  => 'integer',
        'reference_no'        => 'string',
        'booking_type'        => 'string',
        'payee'               => 'string',
        'payment_against'     => 'string',
        'purpose'             => 'string',
        'amount'              => 'float',
        'project_cost_centre' => 'string',
        'booking_date'        => 'date',
        'cheque_number'       => 'string',
        'cheque_date'         => 'date',
        'bank_account'        => 'string',
        'release_date'        => 'date',
        'cash_account'        => 'string',
        'payment_date'        => 'date',
        'status'              => 'integer',
        'created_by'          => 'integer',
    ];

    /**
     * Rules shared by the web form requests and the API controller, so both sides
     * of the module validate identically. Type-specific rules are added by
     * rulesFor() below.
     */
    public static $rules = [
        'booking_type'        => 'required|in:cheque,cash',
        'payee'               => 'required|string|max:255',
        'payment_against'     => 'required|string|max:255',
        'purpose'             => 'required|string|max:2000',
        'amount'              => 'required|numeric|min:0.01',
        'project_cost_centre' => 'required|string|max:255',
        'booking_date'        => 'required|date',
    ];

    /**
     * Full rule set for one booking type. The other type's fields are not merely
     * optional — they must be absent, so a cash booking can never smuggle in a
     * cheque number.
     */
    public static function rulesFor($bookingType)
    {
        $rules = static::$rules;

        if ($bookingType === static::TYPE_CASH) {
            return $rules + [
                'cash_account' => 'required|string|max:255',
                'payment_date' => 'required|date',
            ];
        }

        return $rules + [
            'cheque_number' => 'required|string|max:60',
            'cheque_date'   => 'required|date',
            'bank_account'  => 'required|string|max:255',
            'release_date'  => 'required|date|after_or_equal:cheque_date',
        ];
    }

    public static function statuses()
    {
        return [
            static::STATUS_DRAFT    => 'Draft',
            static::STATUS_PENDING  => 'Pending Verification',
            static::STATUS_VERIFIED => 'Pending Approval',
            static::STATUS_APPROVED => 'Approved',
            static::STATUS_REJECTED => 'Rejected',
            static::STATUS_ON_HOLD  => 'On Hold',
        ];
    }

    public static function types()
    {
        return [
            static::TYPE_CHEQUE => 'Cheque',
            static::TYPE_CASH   => 'Cash',
        ];
    }

    // ---------------------------------------------------------------------
    // Relations
    // ---------------------------------------------------------------------

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(\App\Models\PaymentBookingApproval::class, 'payment_booking_id')
            ->orderBy('level');
    }

    public function verification()
    {
        return $this->hasOne(\App\Models\PaymentBookingApproval::class, 'payment_booking_id')
            ->where('level', PaymentBookingApproval::LEVEL_VERIFY);
    }

    public function approval()
    {
        return $this->hasOne(\App\Models\PaymentBookingApproval::class, 'payment_booking_id')
            ->where('level', PaymentBookingApproval::LEVEL_APPROVE);
    }

    // ---------------------------------------------------------------------
    // Derived attributes
    // ---------------------------------------------------------------------

    public function isCheque()
    {
        return $this->booking_type === static::TYPE_CHEQUE;
    }

    public function isCash()
    {
        return $this->booking_type === static::TYPE_CASH;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = static::statuses();

        return $statuses[$this->status] ?? 'Unknown';
    }

    public function getTypeLabelAttribute()
    {
        return $this->isCash() ? 'Cash' : 'Cheque';
    }

    /**
     * The date the money actually leaves — release_date on a cheque, payment_date
     * on cash. Every released/unreleased figure in the module keys off this.
     */
    public function getEffectiveDateAttribute()
    {
        $date = $this->isCash() ? $this->payment_date : $this->release_date;

        return $date ?: $this->booking_date;
    }

    /**
     * Approved and its effective date has arrived. Anything else is still money
     * committed but not yet out the door.
     */
    public function getIsReleasedAttribute()
    {
        if ($this->status !== static::STATUS_APPROVED) {
            return false;
        }

        $date = $this->effective_date;

        return $date && Carbon::parse($date)->startOfDay()->lte(Carbon::today());
    }

    public function getReleasedAmountAttribute()
    {
        return $this->is_released ? (float) $this->amount : 0.0;
    }

    /**
     * Editing is only allowed while nobody has acted on the booking. Once level 1
     * has cleared it the figures are locked; a rejected booking is reopened for
     * correction and re-submission.
     */
    public function isEditable()
    {
        return in_array($this->status, [
            static::STATUS_DRAFT,
            static::STATUS_PENDING,
            static::STATUS_REJECTED,
        ], true);
    }

    /**
     * The review level this booking is currently waiting on, or null when no
     * decision is outstanding.
     */
    public function pendingLevel()
    {
        if ($this->status === static::STATUS_PENDING) {
            return PaymentBookingApproval::LEVEL_VERIFY;
        }

        if ($this->status === static::STATUS_VERIFIED) {
            return PaymentBookingApproval::LEVEL_APPROVE;
        }

        return null;
    }

    // ---------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------

    public function scopeOfType($query, $type)
    {
        return $query->where('booking_type', $type);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', static::STATUS_APPROVED);
    }

    /**
     * Bookings whose money moves inside the given window, whichever date column
     * applies to their type.
     */
    public function scopeEffectiveBetween($query, $from, $to)
    {
        return $query->where(function ($q) use ($from, $to) {
            $q->where(function ($cheque) use ($from, $to) {
                $cheque->where('booking_type', static::TYPE_CHEQUE)
                    ->whereBetween('release_date', [$from, $to]);
            })->orWhere(function ($cash) use ($from, $to) {
                $cash->where('booking_type', static::TYPE_CASH)
                    ->whereBetween('payment_date', [$from, $to]);
            });
        });
    }

    /**
     * Bookings created by one user — the module only ever shows an accountant
     * their own entries.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
