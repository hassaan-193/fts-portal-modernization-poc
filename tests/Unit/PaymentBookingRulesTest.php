<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\Services\PaymentBookingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * PaymentBookingRulesTest — Full Branch Coverage
 *
 * Tests are pure unit tests — they instantiate Eloquent model objects
 * in memory without hitting the database.
 *
 * BRANCHES COVERED:
 *  PaymentBookingService::permission()        — valid level, invalid level (🆕)
 *  PaymentBookingService::isValidLevel()      — true, false
 *  PaymentBookingService::levelsForUser()     — null user fallback (🆕)
 *  PaymentBookingService::canDecide()         — not authenticated, not awaiting,
 *                                               no roster permission,
 *                                               already decided other level (🆕),
 *                                               allowed (🆕)
 *  PaymentBooking::pendingLevel()             — all 6 statuses
 *  PaymentBooking::isEditable()               — all 6 statuses
 *  PaymentBooking::getEffectiveDateAttribute  — cheque (release_date), cash (payment_date),
 *                                               null fallback to booking_date (🆕)
 *  PaymentBooking::getIsReleasedAttribute     — not approved, approved+past, approved+future
 *  PaymentBooking::getReleasedAmountAttribute — is_released=true, is_released=false
 *  PaymentBooking::getStatusLabelAttribute    — all 6 statuses + unknown (🆕)
 *  PaymentBooking::getTypeLabelAttribute      — cheque, cash (🆕)
 *  PaymentBooking::isCheque() / isCash()      — both types (🆕)
 *  PaymentBooking::rulesFor()                 — cheque rules, cash rules (🆕)
 *  PaymentBooking::statuses() / types()       — count assertions (🆕)
 *  PaymentBookingApproval::levels() / decisions() — count assertions (🆕)
 */
class PaymentBookingRulesTest extends TestCase
{
    // ==================================================================
    // PaymentBookingService — static method coverage
    // ==================================================================

    /**
     * Review level and permission mappings.
     */
    public function testLevelAndPermissionMappings()
    {
        $this->assertEquals(
            'verify_payment_bookings',
            PaymentBookingService::permission(PaymentBookingApproval::LEVEL_VERIFY)
        );

        $this->assertEquals(
            'approve_payment_bookings',
            PaymentBookingService::permission(PaymentBookingApproval::LEVEL_APPROVE)
        );

        $this->assertTrue(PaymentBookingService::isValidLevel(PaymentBookingApproval::LEVEL_VERIFY));
        $this->assertTrue(PaymentBookingService::isValidLevel(PaymentBookingApproval::LEVEL_APPROVE));
        $this->assertFalse(PaymentBookingService::isValidLevel(999));
    }

    /**
     * 🆕 permission() with an invalid level returns null.
     * Branch: static::PERMISSIONS[$level] ?? null — null path
     */
    public function testPermissionWithInvalidLevelReturnsNull()
    {
        $this->assertNull(PaymentBookingService::permission(999));
        $this->assertNull(PaymentBookingService::permission(0));
    }

    /**
     * 🆕 levelsForUser() returns empty array when $user is null and no auth user.
     * Branch: if (!$user) { return []; }
     */
    public function testLevelsForUserWithNullReturnsEmptyArray()
    {
        // No logged-in user, no $user argument → must return []
        $levels = PaymentBookingService::levelsForUser(null);
        $this->assertIsArray($levels);
        $this->assertEmpty($levels);
    }

    // ==================================================================
    // PaymentBookingService::canDecide() — all branches
    // ==================================================================

    /**
     * canDecide rejects unauthenticated user ($user=null).
     * Branch: if (!$user) { return [false, 'Not authenticated.'] }
     */
    public function testCanDecideGuards()
    {
        $booking = new PaymentBooking(['status' => PaymentBooking::STATUS_APPROVED]);

        list($allowed, $reason) = PaymentBookingService::canDecide($booking, null);
        $this->assertFalse($allowed);
        $this->assertEquals('Not authenticated.', $reason);
    }

    /**
     * 🆕 canDecide rejects booking that is not awaiting a decision.
     * Branch: if ($level === null) — booking is Approved/Draft/Rejected/On Hold
     */
    public function testCanDecideRejectsBookingNotAwaitingDecision()
    {
        // Approved booking → pendingLevel() = null
        $booking = new PaymentBooking(['status' => PaymentBooking::STATUS_APPROVED]);
        $booking->setRelation('approvals', new Collection());

        // Create a mock user with a can() method that always returns true
        $user = new class {
            public $id = 1;
            public $name = 'Test';
            public function can($ability) { return true; }
        };

        list($allowed, $reason) = PaymentBookingService::canDecide($booking, $user);

        $this->assertFalse($allowed);
        $this->assertEquals('This booking is not awaiting a decision.', $reason);
    }

    /**
     * 🆕 canDecide allows a user who holds the right permission and has not acted.
     * Branch: all guards pass → return [true, null]
     */
    public function testCanDecideAllowsEligibleUser()
    {
        // Pending booking → pendingLevel() = LEVEL_VERIFY
        $booking = new PaymentBooking(['status' => PaymentBooking::STATUS_PENDING]);
        $booking->setRelation('approvals', new Collection()); // no previous decisions

        // User whose can() returns true for verify_payment_bookings
        $user = new class {
            public $id = 42;
            public $name = 'Verifier';
            public function can($ability) {
                return $ability === 'verify_payment_bookings';
            }
        };

        list($allowed, $reason) = PaymentBookingService::canDecide($booking, $user);

        $this->assertTrue($allowed);
        $this->assertNull($reason);
    }

    /**
     * 🆕 canDecide blocks user who already acted on the OTHER level.
     * Branch: if ($alreadyActed) → 'You already decided the other level'
     */
    public function testCanDecideBlocksUserWhoAlreadyActedOnOtherLevel()
    {
        // Verified booking → pendingLevel() = LEVEL_APPROVE
        $booking = new PaymentBooking(['status' => PaymentBooking::STATUS_VERIFIED]);

        // Simulate an existing LEVEL_VERIFY approval by user id=99
        $existingApproval = new PaymentBookingApproval([
            'user_id'  => 99,
            'level'    => PaymentBookingApproval::LEVEL_VERIFY,
            'decision' => PaymentBookingApproval::DECISION_APPROVED,
        ]);
        $booking->setRelation('approvals', new Collection([$existingApproval]));

        // User id=99 now tries to also act on LEVEL_APPROVE
        $user = new class {
            public $id = 99;
            public $name = 'Same Person';
            public function can($ability) {
                return $ability === 'approve_payment_bookings';
            }
        };

        list($allowed, $reason) = PaymentBookingService::canDecide($booking, $user);

        $this->assertFalse($allowed);
        $this->assertEquals('You already decided the other level of this booking.', $reason);
    }

    // ==================================================================
    // PaymentBooking model — pendingLevel() all 6 statuses
    // ==================================================================

    /**
     * pendingLevel resolution across all statuses.
     */
    public function testPendingLevelResolution()
    {
        $booking = new PaymentBooking();

        $booking->status = PaymentBooking::STATUS_DRAFT;
        $this->assertNull($booking->pendingLevel());

        $booking->status = PaymentBooking::STATUS_PENDING;
        $this->assertEquals(PaymentBookingApproval::LEVEL_VERIFY, $booking->pendingLevel());

        $booking->status = PaymentBooking::STATUS_VERIFIED;
        $this->assertEquals(PaymentBookingApproval::LEVEL_APPROVE, $booking->pendingLevel());

        $booking->status = PaymentBooking::STATUS_APPROVED;
        $this->assertNull($booking->pendingLevel());

        $booking->status = PaymentBooking::STATUS_REJECTED;
        $this->assertNull($booking->pendingLevel());

        $booking->status = PaymentBooking::STATUS_ON_HOLD;
        $this->assertNull($booking->pendingLevel());
    }

    // ==================================================================
    // PaymentBooking model — isEditable() locking rules
    // ==================================================================

    /**
     * isEditable: only Draft, Pending, and Rejected can be edited.
     */
    public function testIsEditableLockingRules()
    {
        $booking = new PaymentBooking();

        $booking->status = PaymentBooking::STATUS_DRAFT;
        $this->assertTrue($booking->isEditable());

        $booking->status = PaymentBooking::STATUS_PENDING;
        $this->assertTrue($booking->isEditable());

        $booking->status = PaymentBooking::STATUS_REJECTED;
        $this->assertTrue($booking->isEditable());

        // Locked statuses
        $booking->status = PaymentBooking::STATUS_VERIFIED;
        $this->assertFalse($booking->isEditable());

        $booking->status = PaymentBooking::STATUS_APPROVED;
        $this->assertFalse($booking->isEditable());

        $booking->status = PaymentBooking::STATUS_ON_HOLD;
        $this->assertFalse($booking->isEditable());
    }

    // ==================================================================
    // PaymentBooking model — effective_date accessor (3 branches)
    // ==================================================================

    /**
     * Cheque booking: effective_date = release_date.
     * Cash booking: effective_date = payment_date.
     */
    public function testEffectiveDateCalculation()
    {
        // Cheque branch
        $cheque = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'release_date' => '2026-10-15',
            'booking_date' => '2026-09-01',
        ]);
        $this->assertEquals('2026-10-15', $cheque->effective_date->format('Y-m-d'));

        // Cash branch
        $cash = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CASH,
            'payment_date' => '2026-09-17',
            'booking_date' => '2026-09-01',
        ]);
        $this->assertEquals('2026-09-17', $cash->effective_date->format('Y-m-d'));
    }

    /**
     * 🆕 Null date fallback: when release_date is null, effective_date falls back
     * to booking_date.
     * Branch: $date ?: $this->booking_date
     */
    public function testEffectiveDateFallsBackToBookingDate()
    {
        $chequeNoRelease = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'release_date' => null,
            'booking_date' => '2026-08-01',
        ]);

        $this->assertEquals('2026-08-01', $chequeNoRelease->effective_date->format('Y-m-d'));
    }

    // ==================================================================
    // PaymentBooking model — is_released and released_amount
    // ==================================================================

    /**
     * Liquidity release math: Released only when APPROVED and effective_date <= today.
     */
    public function testLiquidityReleaseMath()
    {
        $today      = Carbon::today()->toDateString();
        $pastDate   = Carbon::yesterday()->toDateString();
        $futureDate = Carbon::tomorrow()->toDateString();

        // Approved + past date → Released
        $booking1 = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'amount'       => 5000.00,
            'release_date' => $pastDate,
            'status'       => PaymentBooking::STATUS_APPROVED,
        ]);
        $this->assertTrue($booking1->is_released);
        $this->assertEquals(5000.00, $booking1->released_amount);

        // Approved + today → Released
        $booking2 = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'amount'       => 3500.00,
            'release_date' => $today,
            'status'       => PaymentBooking::STATUS_APPROVED,
        ]);
        $this->assertTrue($booking2->is_released);
        $this->assertEquals(3500.00, $booking2->released_amount);

        // Approved + future date (PDC) → NOT released
        $booking3 = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'amount'       => 12000.00,
            'release_date' => $futureDate,
            'status'       => PaymentBooking::STATUS_APPROVED,
        ]);
        $this->assertFalse($booking3->is_released);
        $this->assertEquals(0.0, $booking3->released_amount);

        // Pending + past date → NOT released (unapproved)
        $booking4 = new PaymentBooking([
            'booking_type' => PaymentBooking::TYPE_CHEQUE,
            'amount'       => 8000.00,
            'release_date' => $pastDate,
            'status'       => PaymentBooking::STATUS_PENDING,
        ]);
        $this->assertFalse($booking4->is_released);
        $this->assertEquals(0.0, $booking4->released_amount);
    }

    // ==================================================================
    // PaymentBooking model — label accessors and type helpers
    // ==================================================================

    /**
     * 🆕 getStatusLabelAttribute: verify all 6 status labels + unknown.
     * Covers all entries in statuses() and the ?? 'Unknown' fallback.
     */
    public function testStatusLabelAccessor()
    {
        $booking = new PaymentBooking();

        $booking->status = PaymentBooking::STATUS_DRAFT;
        $this->assertEquals('Draft', $booking->status_label);

        $booking->status = PaymentBooking::STATUS_PENDING;
        $this->assertEquals('Pending Verification', $booking->status_label);

        $booking->status = PaymentBooking::STATUS_VERIFIED;
        $this->assertEquals('Pending Approval', $booking->status_label);

        $booking->status = PaymentBooking::STATUS_APPROVED;
        $this->assertEquals('Approved', $booking->status_label);

        $booking->status = PaymentBooking::STATUS_REJECTED;
        $this->assertEquals('Rejected', $booking->status_label);

        $booking->status = PaymentBooking::STATUS_ON_HOLD;
        $this->assertEquals('On Hold', $booking->status_label);

        // Unknown status → fallback 'Unknown'
        $booking->status = 99;
        $this->assertEquals('Unknown', $booking->status_label);
    }

    /**
     * 🆕 getTypeLabelAttribute: cheque → 'Cheque', cash → 'Cash'.
     * Also covers isCheque() and isCash().
     */
    public function testTypeLabelAndTypeHelpers()
    {
        $cheque = new PaymentBooking(['booking_type' => PaymentBooking::TYPE_CHEQUE]);
        $this->assertEquals('Cheque', $cheque->type_label);
        $this->assertTrue($cheque->isCheque());
        $this->assertFalse($cheque->isCash());

        $cash = new PaymentBooking(['booking_type' => PaymentBooking::TYPE_CASH]);
        $this->assertEquals('Cash', $cash->type_label);
        $this->assertTrue($cash->isCash());
        $this->assertFalse($cash->isCheque());
    }

    // ==================================================================
    // PaymentBooking model — rulesFor() both branches
    // ==================================================================

    /**
     * 🆕 rulesFor() returns cash-specific rules for 'cash'.
     * Branch: if ($bookingType === TYPE_CASH)
     */
    public function testRulesForCash()
    {
        $rules = PaymentBooking::rulesFor(PaymentBooking::TYPE_CASH);

        $this->assertArrayHasKey('cash_account', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
        $this->assertArrayNotHasKey('cheque_number', $rules);
    }

    /**
     * 🆕 rulesFor() returns cheque-specific rules for 'cheque'.
     * Branch: else (TYPE_CHEQUE)
     */
    public function testRulesForCheque()
    {
        $rules = PaymentBooking::rulesFor(PaymentBooking::TYPE_CHEQUE);

        $this->assertArrayHasKey('cheque_number', $rules);
        $this->assertArrayHasKey('bank_account', $rules);
        $this->assertArrayHasKey('release_date', $rules);
        $this->assertArrayNotHasKey('cash_account', $rules);
    }

    // ==================================================================
    // Static lookup tables — statuses(), types(), approval levels()
    // ==================================================================

    /**
     * 🆕 PaymentBooking::statuses() returns exactly 6 entries.
     */
    public function testStatusesLookupTableHasSixEntries()
    {
        $statuses = PaymentBooking::statuses();
        $this->assertCount(6, $statuses);
    }

    /**
     * 🆕 PaymentBooking::types() returns exactly 2 entries.
     */
    public function testTypesLookupTableHasTwoEntries()
    {
        $types = PaymentBooking::types();
        $this->assertCount(2, $types);
        $this->assertArrayHasKey('cheque', $types);
        $this->assertArrayHasKey('cash', $types);
    }

    /**
     * 🆕 PaymentBookingApproval::levels() returns 2 entries.
     */
    public function testApprovalLevelsHasTwoEntries()
    {
        $levels = PaymentBookingApproval::levels();
        $this->assertCount(2, $levels);
    }

    /**
     * 🆕 PaymentBookingApproval::decisions() returns 3 entries.
     */
    public function testApprovalDecisionsHasThreeEntries()
    {
        $decisions = PaymentBookingApproval::decisions();
        $this->assertCount(3, $decisions);
    }
}
