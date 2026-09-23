<?php

namespace App\Services;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\Services\PaymentBookingNotifier;

/**
 * Everything the Payment Booking module knows about references, review and money.
 *
 * The web controller and the mobile API both go through here, so a booking behaves
 * identically whichever side it was created from.
 *
 * REVIEW MODEL — sequential, two levels:
 *
 *   verify_payment_bookings   (level 1, "Payment Booking Verifier")
 *   approve_payment_bookings  (level 2, "Payment Booking Approver")
 *
 * A booking is submitted as Pending. Any level-1 holder verifies it, which moves it
 * to Pending Approval; any level-2 holder then approves it, which makes it Approved.
 * A rejection at either level rejects the whole booking and reopens it for the
 * creator to correct and re-submit. A hold parks it without ending the cycle.
 *
 * Assign the roster from /users by ticking the two roles — no code change needed.
 */
class PaymentBookingService
{
    /**
     * review level => permission that lets a user act on it.
     */
    const PERMISSIONS = [
        PaymentBookingApproval::LEVEL_VERIFY  => 'verify_payment_bookings',
        PaymentBookingApproval::LEVEL_APPROVE => 'approve_payment_bookings',
    ];

    // ---------------------------------------------------------------------
    // Reference numbers
    // ---------------------------------------------------------------------

    /**
     * Next reference for a booking type, e.g. CHQ-2026-0085 / CSH-2026-0085.
     *
     * The counter runs per type per calendar year. Generated inside the same
     * transaction as the insert (see store()) with a locked read, so two accountants
     * submitting at once cannot land on the same number.
     */
    public static function nextReference($bookingType, $year = null)
    {
        $year   = $year ?: (int) date('Y');
        $prefix = $bookingType === PaymentBooking::TYPE_CASH ? 'CSH' : 'CHQ';
        $stem   = $prefix . '-' . $year . '-';

        $last = PaymentBooking::withTrashed()
            ->where('reference_no', 'like', $stem . '%')
            ->orderByRaw('LENGTH(reference_no) DESC')
            ->orderBy('reference_no', 'desc')
            ->lockForUpdate()
            ->value('reference_no');

        $next = $last ? ((int) substr($last, strlen($stem))) + 1 : 1;

        return $stem . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ---------------------------------------------------------------------
    // Roster / authorisation
    // ---------------------------------------------------------------------

    public static function levels()
    {
        return array_keys(static::PERMISSIONS);
    }

    public static function isValidLevel($level)
    {
        return array_key_exists((int) $level, static::PERMISSIONS);
    }

    public static function permission($level)
    {
        return static::PERMISSIONS[(int) $level] ?? null;
    }

    /**
     * Everyone holding the permission for a level. Resolved through spatie's scope
     * so it covers the permission held directly or via any role.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function roster($level)
    {
        static $cache = [];

        $level = (int) $level;

        if (!array_key_exists($level, $cache)) {
            try {
                $cache[$level] = User::permission(static::permission($level))->orderBy('name')->get();
            } catch (\Exception $e) {
                // Permission row missing (migration not run yet) — treat as no roster.
                $cache[$level] = collect();
            }
        }

        return $cache[$level];
    }

    /**
     * The review levels the given user may act on.
     */
    public static function levelsForUser($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return [];
        }

        return array_values(array_filter(static::levels(), function ($level) use ($user) {
            return $user->can(static::permission($level));
        }));
    }

    /**
     * Whether a user may record a decision at a level — the authorisation boundary.
     */
    public static function canActOnLevel($level, $user = null)
    {
        $user = $user ?: auth()->user();

        return $user && static::isValidLevel($level) && $user->can(static::permission($level));
    }

    /**
     * Whether this specific user may decide this specific booking right now.
     *
     * Beyond holding the permission, the booking has to actually be waiting on that
     * level, and the same person may never clear both levels of one booking — a
     * two-person rule that survives someone holding both roles.
     *
     * @return array [bool $allowed, string|null $reason]
     */
    public static function canDecide(PaymentBooking $booking, $user = null)
    {
        $user  = $user ?: auth()->user();
        $level = $booking->pendingLevel();

        if (!$user) {
            return [false, 'Not authenticated.'];
        }

        if ($level === null) {
            return [false, 'This booking is not awaiting a decision.'];
        }

        if (!static::canActOnLevel($level, $user)) {
            return [false, 'You are not on the roster for this review level.'];
        }

        $alreadyActed = $booking->approvals
            ->where('user_id', (int) $user->id)
            ->where('level', '!=', $level)
            ->isNotEmpty();

        if ($alreadyActed) {
            return [false, 'You already decided the other level of this booking.'];
        }

        return [true, null];
    }

    /**
     * Scope a query to the review queue for one user.
     *
     * `$filter === 'all'` widens it to every booking that has entered the chain.
     * Otherwise it returns only what this reviewer can act on *right now*: the
     * statuses matching the levels they hold, minus any booking whose other level
     * they already decided — those are waiting on somebody else, and leaving them in
     * would make a "needs my action" count overstate the work.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeQueue($query, $user = null, $filter = 'pending')
    {
        $user = $user ?: auth()->user();

        if ($filter === 'all') {
            return $query->where('status', '!=', PaymentBooking::STATUS_DRAFT);
        }

        $levels   = static::levelsForUser($user);
        $statuses = [];

        if (in_array(PaymentBookingApproval::LEVEL_VERIFY, $levels, true)) {
            $statuses[] = PaymentBooking::STATUS_PENDING;
        }

        if (in_array(PaymentBookingApproval::LEVEL_APPROVE, $levels, true)) {
            $statuses[] = PaymentBooking::STATUS_VERIFIED;
        }

        // An empty roster must yield an empty queue, never the whole table.
        $query->whereIn('status', $statuses ?: [-1]);

        // Drop anything this user already decided at the other level — the
        // two-person rule means they can never be the one to close it out.
        if ($user) {
            $query->whereDoesntHave('approvals', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    // ---------------------------------------------------------------------
    // Lifecycle
    // ---------------------------------------------------------------------

    /**
     * Create a booking. `$submit` false parks it as a draft; true puts it straight
     * into the review queue.
     */
    public static function store(array $input, $userId, $submit = true)
    {
        return DB::transaction(function () use ($input, $userId, $submit) {
            $type = ($input['booking_type'] ?? null) === PaymentBooking::TYPE_CASH
                ? PaymentBooking::TYPE_CASH
                : PaymentBooking::TYPE_CHEQUE;

            $input = static::clearUnusedTypeFields($input, $type);

            $input['booking_type'] = $type;
            $input['reference_no'] = static::nextReference($type);
            $input['created_by']   = $userId;
            $input['status']       = $submit ? PaymentBooking::STATUS_PENDING : PaymentBooking::STATUS_DRAFT;
            $input['submitted_at'] = $submit ? Carbon::now() : null;

            $booking = PaymentBooking::create($input);

            // A booking created straight into the queue is the event reviewers
            // need to hear about. Drafts are not — nobody is waiting on them.
            if ($submit) {
                app(PaymentBookingNotifier::class)->submitted($booking);
            }

            return $booking;
        });
    }

    /**
     * Update an editable booking. The reference, type and review state are never
     * touched here — a booking cannot change from cheque to cash once it exists.
     */
    public static function update(PaymentBooking $booking, array $input)
    {
        $input = static::clearUnusedTypeFields($input, $booking->booking_type);

        unset(
            $input['reference_no'], $input['booking_type'], $input['status'],
            $input['created_by'], $input['submitted_at'], $input['verified_at'],
            $input['approved_at'], $input['rejected_at']
        );

        $booking->fill($input)->save();

        return $booking->fresh();
    }

    /**
     * Move a draft or rejected booking into the review queue, clearing any decisions
     * from the previous cycle so the chain shows only the current round.
     */
    public static function submit(PaymentBooking $booking)
    {
        return DB::transaction(function () use ($booking) {
            PaymentBookingApproval::where('payment_booking_id', $booking->id)->delete();

            $booking->update([
                'status'       => PaymentBooking::STATUS_PENDING,
                'submitted_at' => Carbon::now(),
                'verified_at'  => null,
                'approved_at'  => null,
                'rejected_at'  => null,
            ]);

            $fresh = $booking->fresh('approvals');
            app(PaymentBookingNotifier::class)->submitted($fresh);

            return $fresh;
        });
    }

    /**
     * Record one level's decision and re-derive the booking's status.
     *
     * @param  int $decision PaymentBookingApproval::DECISION_*
     * @return PaymentBooking
     */
    public static function decide(PaymentBooking $booking, $userId, $decision, $note = null)
    {
        return DB::transaction(function () use ($booking, $userId, $decision, $note) {
            $level = $booking->pendingLevel();
            $now   = Carbon::now();

            PaymentBookingApproval::updateOrCreate(
                ['payment_booking_id' => $booking->id, 'level' => $level],
                [
                    'user_id'    => $userId,
                    'decision'   => $decision,
                    'note'       => $note,
                    'decided_at' => $now,
                ]
            );

            if ($decision === PaymentBookingApproval::DECISION_REJECTED) {
                $booking->update([
                    'status'      => PaymentBooking::STATUS_REJECTED,
                    'rejected_at' => $now,
                ]);
            } elseif ($decision === PaymentBookingApproval::DECISION_HOLD) {
                // Held bookings keep their place in the chain; resuming replays the
                // level they were parked at.
                $booking->update(['status' => PaymentBooking::STATUS_ON_HOLD]);
            } elseif ($level === PaymentBookingApproval::LEVEL_VERIFY) {
                $booking->update([
                    'status'      => PaymentBooking::STATUS_VERIFIED,
                    'verified_at' => $now,
                ]);
            } else {
                $booking->update([
                    'status'      => PaymentBooking::STATUS_APPROVED,
                    'approved_at' => $now,
                ]);
            }

            $fresh = $booking->fresh('approvals');
            $notifier = app(PaymentBookingNotifier::class);

            if ($decision === PaymentBookingApproval::DECISION_APPROVED
                && $level === PaymentBookingApproval::LEVEL_VERIFY) {
                // Cleared level 1 — hand it to the approvers.
                $notifier->readyForApproval($fresh);
            } else {
                // Rejected, held, or finally approved: the creator's business.
                $notifier->decided($fresh, $decision, $note);
            }

            return $fresh;
        });
    }

    /**
     * Take a held booking back into the queue at whichever level parked it.
     */
    public static function resume(PaymentBooking $booking)
    {
        return DB::transaction(function () use ($booking) {
            $held = PaymentBookingApproval::where('payment_booking_id', $booking->id)
                ->where('decision', PaymentBookingApproval::DECISION_HOLD)
                ->orderBy('level')
                ->first();

            $level = $held ? (int) $held->level : PaymentBookingApproval::LEVEL_VERIFY;

            if ($held) {
                $held->delete();
            }

            $booking->update([
                'status' => $level === PaymentBookingApproval::LEVEL_APPROVE
                    ? PaymentBooking::STATUS_VERIFIED
                    : PaymentBooking::STATUS_PENDING,
            ]);

            return $booking->fresh('approvals');
        });
    }

    /**
     * The chain as the detail screens and the mobile app render it: one entry per
     * level, with the decision if one has been made.
     */
    public static function chain(PaymentBooking $booking)
    {
        $decisions = $booking->relationLoaded('approvals')
            ? $booking->approvals->keyBy('level')
            : $booking->approvals()->get()->keyBy('level');

        $pending = $booking->pendingLevel();
        $chain   = [];

        foreach (PaymentBookingApproval::levels() as $level => $label) {
            $decision = $decisions->get($level);

            if ($decision) {
                $state = $decision->decision_label;
            } elseif ($booking->status === PaymentBooking::STATUS_REJECTED) {
                $state = 'Not reached';
            } elseif ($level === $pending) {
                $state = 'Pending';
            } else {
                $state = 'Waiting';
            }

            $chain[] = [
                'level'       => $level,
                'level_label' => $label,
                'role'        => $level === PaymentBookingApproval::LEVEL_VERIFY
                    ? 'Payment Booking Verifier'
                    : 'Payment Booking Approver',
                'state'       => $state,
                'decided_by'  => $decision && $decision->user ? $decision->user->name : null,
                'decided_at'  => $decision && $decision->decided_at
                    ? $decision->decided_at->toDateTimeString()
                    : null,
                'note'        => $decision ? $decision->note : null,
            ];
        }

        return $chain;
    }

    // ---------------------------------------------------------------------
    // Money
    // ---------------------------------------------------------------------

    /**
     * Released / upcoming figures for the dashboard and the mobile home screen.
     *
     * "Released" means approved AND the effective date has passed; a booking sitting
     * in the review chain is committed but not released, however near its date is.
     *
     * @param  int|null $userId Restrict to one creator's bookings (null = everyone).
     */
    public static function summary($userId = null, Carbon $reference = null)
    {
        $now   = $reference ? $reference->copy() : Carbon::now();
        $today = $now->copy()->startOfDay();

        $thisMonth = [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()];
        $nextMonth = [
            $now->copy()->addMonthNoOverflow()->startOfMonth()->toDateString(),
            $now->copy()->addMonthNoOverflow()->endOfMonth()->toDateString(),
        ];

        $base = function () use ($userId) {
            $query = PaymentBooking::query();

            if ($userId) {
                $query->ownedBy($userId);
            }

            return $query;
        };

        // Money already out the door this month: approved, dated in this month, date reached.
        $releasedThisMonth = (clone $base())->approved()
            ->effectiveBetween($thisMonth[0], min($thisMonth[1], $today->toDateString()))
            ->sum('amount');

        // Approved but still ahead of its date, inside this month.
        $pendingReleaseThisMonth = (clone $base())->approved()
            ->effectiveBetween($today->copy()->addDay()->toDateString(), $thisMonth[1])
            ->sum('amount');

        $releasedNextMonth = (clone $base())->approved()
            ->effectiveBetween($nextMonth[0], $nextMonth[1])
            ->sum('amount');

        // Everything still in the chain — committed but unapproved, any date.
        $awaitingReview = (clone $base())
            ->whereIn('status', [
                PaymentBooking::STATUS_PENDING,
                PaymentBooking::STATUS_VERIFIED,
                PaymentBooking::STATUS_ON_HOLD,
            ])->sum('amount');

        $bookedThisMonth = (clone $base())
            ->whereBetween('booking_date', $thisMonth)
            ->whereNotIn('status', [PaymentBooking::STATUS_DRAFT, PaymentBooking::STATUS_REJECTED])
            ->sum('amount');

        // Approved, dated on or before today, but only counting what is genuinely
        // overdue for release is not meaningful here — instead report the whole
        // approved-but-future pipeline so the two figures below always reconcile.
        $notReleased = (clone $base())->approved()
            ->where(function ($q) use ($today) {
                $q->where(function ($cheque) use ($today) {
                    $cheque->where('booking_type', PaymentBooking::TYPE_CHEQUE)
                        ->whereDate('release_date', '>', $today->toDateString());
                })->orWhere(function ($cash) use ($today) {
                    $cash->where('booking_type', PaymentBooking::TYPE_CASH)
                        ->whereDate('payment_date', '>', $today->toDateString());
                });
            })->sum('amount');

        return [
            'as_of'                      => $today->toDateString(),
            'booked_this_month'          => round((float) $bookedThisMonth, 2),
            'released_this_month'        => round((float) $releasedThisMonth, 2),
            'pending_release_this_month' => round((float) $pendingReleaseThisMonth, 2),
            'released_next_month'        => round((float) $releasedNextMonth, 2),
            'not_released'               => round((float) $notReleased, 2),
            'awaiting_review'            => round((float) $awaitingReview, 2),
            'counts'                     => [
                'total'    => (clone $base())->count(),
                'draft'    => (clone $base())->where('status', PaymentBooking::STATUS_DRAFT)->count(),
                'pending'  => (clone $base())->where('status', PaymentBooking::STATUS_PENDING)->count(),
                'verified' => (clone $base())->where('status', PaymentBooking::STATUS_VERIFIED)->count(),
                'approved' => (clone $base())->where('status', PaymentBooking::STATUS_APPROVED)->count(),
                'rejected' => (clone $base())->where('status', PaymentBooking::STATUS_REJECTED)->count(),
                'on_hold'  => (clone $base())->where('status', PaymentBooking::STATUS_ON_HOLD)->count(),
            ],
        ];
    }

    /**
     * Approved amounts bucketed by the month the money moves, for the dashboard's
     * release schedule. Returns `$months` buckets starting from the current month,
     * or a custom date range if $fromMonth/$toMonth are provided.
     *
     * @param int|null $userId Restrict to one creator's bookings (null = everyone).
     * @param int $months Number of months to show (default 6)
     * @param Carbon|null $reference Reference date (default now)
     * @param string|null $fromMonth Custom start month as 'YYYY-MM' (overrides $months)
     * @param string|null $toMonth Custom end month as 'YYYY-MM' (overrides $months)
     */
    public static function releaseSchedule($userId = null, $months = 6, Carbon $reference = null, $fromMonth = null, $toMonth = null)
    {
        $now    = $reference ? $reference->copy() : Carbon::now();
        $today  = $now->copy()->startOfDay();
        $result = [];

        if ($fromMonth && $toMonth) {
            $startDate = Carbon::createFromFormat('Y-m', $fromMonth)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $toMonth)->endOfMonth();
            $cursor = $startDate->copy();

            while ($cursor->lte($endDate)) {
                $from = $cursor->copy()->startOfMonth();
                $to = $cursor->copy()->endOfMonth();

                $query = PaymentBooking::query()->approved()
                    ->effectiveBetween($from->toDateString(), $to->toDateString());

                if ($userId) {
                    $query->ownedBy($userId);
                }

                $bookings = $query->get(['amount', 'booking_type', 'release_date', 'payment_date']);

                $released = $bookings->filter(function ($b) use ($today) {
                    $date = $b->effective_date;

                    return $date && Carbon::parse($date)->startOfDay()->lte($today);
                })->sum('amount');

                $result[] = [
                    'month'        => $cursor->format('Y-m'),
                    'label'        => $cursor->format('M Y'),
                    'total'        => round((float) $bookings->sum('amount'), 2),
                    'released'     => round((float) $released, 2),
                    'not_released' => round((float) $bookings->sum('amount') - $released, 2),
                    'count'        => $bookings->count(),
                ];

                $cursor->addMonthNoOverflow();
            }
        } else {
            for ($i = 0; $i < $months; $i++) {
                $cursor = $now->copy()->addMonthsNoOverflow($i);
                $from   = $cursor->copy()->startOfMonth();
                $to     = $cursor->copy()->endOfMonth();

                $query = PaymentBooking::query()->approved()
                    ->effectiveBetween($from->toDateString(), $to->toDateString());

                if ($userId) {
                    $query->ownedBy($userId);
                }

                $bookings = $query->get(['amount', 'booking_type', 'release_date', 'payment_date']);

                $released = $bookings->filter(function ($b) use ($today) {
                    $date = $b->effective_date;

                    return $date && Carbon::parse($date)->startOfDay()->lte($today);
                })->sum('amount');

                $result[] = [
                    'month'        => $cursor->format('Y-m'),
                    'label'        => $cursor->format('M Y'),
                    'total'        => round((float) $bookings->sum('amount'), 2),
                    'released'     => round((float) $released, 2),
                    'not_released' => round((float) $bookings->sum('amount') - $released, 2),
                    'count'        => $bookings->count(),
                ];
            }
        }

        return $result;
    }

    /**
     * The next approved payments whose money is due to move, for the dashboard's
     * "due to release" list.
     *
     * Lives here rather than on a controller so the portal and the mobile API
     * show the same rows — the same rule the rest of this service follows.
     *
     * @param  int|null $userId Restrict to one creator (null = everyone).
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function upcomingReleases($userId = null, $limit = 10, Carbon $reference = null)
    {
        $today = ($reference ? $reference->copy() : Carbon::today())->startOfDay();

        $query = PaymentBooking::approved()->with('creator');

        if ($userId) {
            $query->ownedBy($userId);
        }

        return $query->effectiveBetween(
                $today->toDateString(),
                $today->copy()->addMonthsNoOverflow(2)->endOfMonth()->toDateString()
            )
            // Whichever date applies to the type; booking_date is the last resort.
            ->orderByRaw('COALESCE(release_date, payment_date, booking_date) ASC')
            ->limit($limit)
            ->get();
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Null out the columns belonging to the other booking type, so a cash booking
     * never carries a stale cheque number after an edit.
     */
    private static function clearUnusedTypeFields(array $input, $type)
    {
        $chequeFields = ['cheque_number', 'cheque_date', 'bank_account', 'release_date'];
        $cashFields   = ['cash_account', 'payment_date'];

        foreach (($type === PaymentBooking::TYPE_CASH ? $chequeFields : $cashFields) as $field) {
            $input[$field] = null;
        }

        return $input;
    }
}
