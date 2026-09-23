<?php

namespace Tests\Unit;

use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\Services\FcmService;
use App\Services\PaymentBookingNotifier;
use Tests\TestCase;

class PaymentBookingNotifierTest extends TestCase
{
    /** @var \Mockery\MockInterface|FcmService */
    protected $fcmMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fcmMock = \Mockery::mock(FcmService::class);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_constructs_with_default_fcm_service_when_null_passed()
    {
        $this->app->instance(FcmService::class, $this->fcmMock);
        $notifier = new PaymentBookingNotifier();

        $refProperty = new \ReflectionProperty(PaymentBookingNotifier::class, 'fcm');
        $refProperty->setAccessible(true);

        $this->assertSame($this->fcmMock, $refProperty->getValue($notifier));
    }

    /** @test */
    public function it_constructs_with_injected_fcm_service()
    {
        $customFcm = \Mockery::mock(FcmService::class);
        $notifier = new PaymentBookingNotifier($customFcm);

        $refProperty = new \ReflectionProperty(PaymentBookingNotifier::class, 'fcm');
        $refProperty->setAccessible(true);

        $this->assertSame($customFcm, $refProperty->getValue($notifier));
    }

    /** @test */
    public function it_returns_null_from_submitted_when_roster_is_empty()
    {
        $notifier = new PaymentBookingNotifier($this->fcmMock);

        $booking = new PaymentBooking([
            'reference_no' => 'PB-2026-001',
            'payee' => 'Alpha Contracting',
            'amount' => 15000.50,
        ]);

        $result = $notifier->submitted($booking);
        $this->assertNull($result);
    }

    /** @test */
    public function it_returns_null_from_ready_for_approval_when_roster_is_empty()
    {
        $notifier = new PaymentBookingNotifier($this->fcmMock);

        $booking = new PaymentBooking([
            'reference_no' => 'PB-2026-002',
            'payee' => 'Beta Logistics',
            'amount' => 8400.00,
        ]);

        $result = $notifier->readyForApproval($booking);
        $this->assertNull($result);
    }

    /** @test */
    public function it_returns_null_from_decided_when_created_by_is_missing()
    {
        $notifier = new PaymentBookingNotifier($this->fcmMock);

        $booking = new PaymentBooking([
            'reference_no' => 'PB-2026-003',
            'created_by' => null,
        ]);

        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_REJECTED, 'Budget exceeded');
        $this->assertNull($result);
    }

    /** @test */
    public function it_sends_notification_on_rejected_decision_with_note()
    {
        $booking = new PaymentBooking([
            'reference_no' => 'PB-2026-004',
            'created_by' => 42,
            'status' => PaymentBooking::STATUS_REJECTED,
        ]);
        $booking->id = 101;

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [42],
                'Payment booking rejected',
                \Mockery::type('string'),
                \Mockery::on(function ($data) {
                    return $data['event'] === 'rejected' && $data['booking_id'] === 101;
                })
            )
            ->andReturn(['success' => 1, 'failure' => 0]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_REJECTED, 'Missing tax invoice');

        $this->assertEquals(['success' => 1, 'failure' => 0], $result);
    }

    /** @test */
    public function it_sends_notification_on_rejected_decision_without_note()
    {
        $booking = new PaymentBooking([
            'id' => 102,
            'reference_no' => 'PB-2026-005',
            'created_by' => 42,
            'status' => PaymentBooking::STATUS_REJECTED,
        ]);

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [42],
                'Payment booking rejected',
                'PB-2026-005 was rejected.',
                \Mockery::on(function ($data) {
                    return $data['event'] === 'rejected';
                })
            )
            ->andReturn(['success' => 1]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_REJECTED, null);

        $this->assertEquals(['success' => 1], $result);
    }

    /** @test */
    public function it_sends_notification_on_hold_decision_with_note()
    {
        $booking = new PaymentBooking([
            'id' => 103,
            'reference_no' => 'PB-2026-006',
            'created_by' => 7,
            'status' => PaymentBooking::STATUS_PENDING,
        ]);

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [7],
                'Payment booking put on hold',
                'PB-2026-006 — Awaiting client payment',
                \Mockery::on(function ($data) {
                    return $data['event'] === 'held';
                })
            )
            ->andReturn(['success' => 1]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_HOLD, 'Awaiting client payment');

        $this->assertEquals(['success' => 1], $result);
    }

    /** @test */
    public function it_sends_notification_on_hold_decision_without_note()
    {
        $booking = new PaymentBooking([
            'id' => 104,
            'reference_no' => 'PB-2026-007',
            'created_by' => 7,
            'status' => PaymentBooking::STATUS_PENDING,
        ]);

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [7],
                'Payment booking put on hold',
                'PB-2026-007 is on hold.',
                \Mockery::on(function ($data) {
                    return $data['event'] === 'held';
                })
            )
            ->andReturn(['success' => 1]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_HOLD, null);

        $this->assertEquals(['success' => 1], $result);
    }

    /** @test */
    public function it_returns_null_on_default_decision_when_booking_status_is_not_approved()
    {
        $booking = new PaymentBooking([
            'id' => 105,
            'reference_no' => 'PB-2026-008',
            'created_by' => 15,
            'status' => PaymentBooking::STATUS_PENDING, // Not approved
        ]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_APPROVED);

        $this->assertNull($result);
    }

    /** @test */
    public function it_sends_notification_on_default_decision_when_booking_status_is_approved()
    {
        $booking = new PaymentBooking([
            'id' => 106,
            'reference_no' => 'PB-2026-009',
            'amount' => 5250.75,
            'created_by' => 15,
            'status' => PaymentBooking::STATUS_APPROVED,
        ]);

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [15],
                'Payment booking approved',
                'PB-2026-009 · AED 5,250.75 is approved for release.',
                \Mockery::on(function ($data) {
                    return $data['event'] === 'approved' && $data['status'] === PaymentBooking::STATUS_APPROVED;
                })
            )
            ->andReturn(['success' => 1]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $notifier->decided($booking, PaymentBookingApproval::DECISION_APPROVED);

        $this->assertEquals(['success' => 1], $result);
    }

    /** @test */
    public function push_method_filters_unique_non_empty_user_ids()
    {
        $booking = new PaymentBooking([
            'id' => 107,
            'reference_no' => 'PB-2026-010',
            'status' => PaymentBooking::STATUS_PENDING,
        ]);

        $refMethod = new \ReflectionMethod(PaymentBookingNotifier::class, 'push');
        $refMethod->setAccessible(true);

        // Call push with duplicate, zero, and null user IDs: [1, 2, 0, null, 1, 3] -> filtered to [1, 2, 3]
        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->with(
                [1, 2, 3],
                'Test Title',
                'Test Body',
                \Mockery::any()
            )
            ->andReturn(['sent' => true]);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $refMethod->invoke($notifier, [1, 2, 0, null, 1, 3], 'Test Title', 'Test Body', $booking, 'custom_event');

        $this->assertEquals(['sent' => true], $result);
    }

    /** @test */
    public function push_method_returns_null_when_filtered_user_ids_are_empty()
    {
        $booking = new PaymentBooking([
            'id' => 108,
            'reference_no' => 'PB-2026-011',
            'status' => PaymentBooking::STATUS_PENDING,
        ]);

        $refMethod = new \ReflectionMethod(PaymentBookingNotifier::class, 'push');
        $refMethod->setAccessible(true);

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $refMethod->invoke($notifier, [0, null, ''], 'Title', 'Body', $booking, 'event');

        $this->assertNull($result);
    }

    /** @test */
    public function push_method_catches_exception_and_returns_null_without_breaking()
    {
        $booking = new PaymentBooking([
            'id' => 109,
            'reference_no' => 'PB-2026-012',
            'status' => PaymentBooking::STATUS_PENDING,
        ]);

        $refMethod = new \ReflectionMethod(PaymentBookingNotifier::class, 'push');
        $refMethod->setAccessible(true);

        $this->fcmMock->shouldReceive('sendToUsers')
            ->once()
            ->andThrow(new \Exception('Firebase server unreachable'));

        $notifier = new PaymentBookingNotifier($this->fcmMock);
        $result = $refMethod->invoke($notifier, [10], 'Title', 'Body', $booking, 'test_event');

        $this->assertNull($result);
    }
}
