<?php

namespace App\Models;

use Eloquent as Model;

/**
 * One level's decision on a payment booking.
 *
 * Uniqueness is enforced on (payment_booking_id, level), so each level holds
 * exactly one standing decision. Re-submitting after a rejection clears the old
 * rows rather than stacking new ones — see PaymentBookingService::submit().
 */
class PaymentBookingApproval extends Model
{
    const LEVEL_VERIFY  = 1;
    const LEVEL_APPROVE = 2;

    const DECISION_APPROVED = 1;
    const DECISION_REJECTED = 2;
    const DECISION_HOLD     = 3;

    public $table = 'payment_booking_approvals';

    public $fillable = [
        'payment_booking_id',
        'level',
        'user_id',
        'decision',
        'note',
        'decided_at',
    ];

    protected $casts = [
        'id'                 => 'integer',
        'payment_booking_id' => 'integer',
        'level'              => 'integer',
        'user_id'            => 'integer',
        'decision'           => 'integer',
        'note'               => 'string',
    ];

    protected $dates = ['decided_at'];

    public static function levels()
    {
        return [
            static::LEVEL_VERIFY  => 'Verification',
            static::LEVEL_APPROVE => 'Approval',
        ];
    }

    public static function decisions()
    {
        return [
            static::DECISION_APPROVED => 'Approved',
            static::DECISION_REJECTED => 'Rejected',
            static::DECISION_HOLD     => 'On Hold',
        ];
    }

    public function getLevelLabelAttribute()
    {
        $levels = static::levels();

        return $levels[$this->level] ?? 'Unknown';
    }

    public function getDecisionLabelAttribute()
    {
        $decisions = static::decisions();

        return $decisions[$this->decision] ?? 'Unknown';
    }

    public function booking()
    {
        return $this->belongsTo(\App\Models\PaymentBooking::class, 'payment_booking_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
