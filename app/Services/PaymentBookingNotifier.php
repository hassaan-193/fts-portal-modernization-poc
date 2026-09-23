<?php

namespace App\Services;

use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use Illuminate\Support\Facades\Log;

/**
 * Turns payment-booking events into push notifications.
 *
 * Who gets told, and when:
 *
 *   submitted            -> every Payment Booking Verifier   (level 1's queue)
 *   level 1 approved     -> every Payment Booking Approver   (level 2's queue)
 *   level 2 approved     -> the creator ("it is approved")
 *   rejected / held      -> the creator, carrying the reason
 *   resumed              -> whichever level it went back to
 *
 * Every method here is best-effort. A booking decision must never fail because
 * a notification could not be delivered, so everything is wrapped and logged.
 */
class PaymentBookingNotifier
{
    /** @var FcmService */
    private $fcm;

    public function __construct(FcmService $fcm = null)
    {
        $this->fcm = $fcm ?: app(FcmService::class);
    }

    /**
     * A booking has entered the queue at level 1 — tell the verifiers.
     *
     * This is the event that matters most: a payment sitting unreviewed is the
     * thing the whole module exists to prevent.
     */
    public function submitted(PaymentBooking $booking)
    {
        $userIds = PaymentBookingService::roster(PaymentBookingApproval::LEVEL_VERIFY)
            ->pluck('id')->map('intval')->all();

        return $this->push(
            $userIds,
            'New payment booking to verify',
            sprintf('%s · %s · AED %s',
                $booking->reference_no,
                $booking->payee,
                number_format($booking->amount, 2)
            ),
            $booking,
            'submitted'
        );
    }

    /**
     * Level 1 cleared — it is now the approvers' problem.
     */
    public function readyForApproval(PaymentBooking $booking)
    {
        $userIds = PaymentBookingService::roster(PaymentBookingApproval::LEVEL_APPROVE)
            ->pluck('id')->map('intval')->all();

        return $this->push(
            $userIds,
            'Payment booking ready for approval',
            sprintf('%s · %s · AED %s',
                $booking->reference_no,
                $booking->payee,
                number_format($booking->amount, 2)
            ),
            $booking,
            'ready_for_approval'
        );
    }

    /**
     * A decision the creator needs to know about.
     */
    public function decided(PaymentBooking $booking, $decision, $note = null)
    {
        if (!$booking->created_by) {
            return null;
        }

        switch ($decision) {
            case PaymentBookingApproval::DECISION_REJECTED:
                $title = 'Payment booking rejected';
                // The reason is the useful part — lead with it.
                $body = $note
                    ? sprintf('%s — %s', $booking->reference_no, $note)
                    : sprintf('%s was rejected.', $booking->reference_no);
                $event = 'rejected';
                break;

            case PaymentBookingApproval::DECISION_HOLD:
                $title = 'Payment booking put on hold';
                $body = $note
                    ? sprintf('%s — %s', $booking->reference_no, $note)
                    : sprintf('%s is on hold.', $booking->reference_no);
                $event = 'held';
                break;

            default:
                // Only the final approval is worth telling the creator about;
                // clearing level 1 is not news to them.
                if ($booking->status !== PaymentBooking::STATUS_APPROVED) {
                    return null;
                }
                $title = 'Payment booking approved';
                $body = sprintf('%s · AED %s is approved for release.',
                    $booking->reference_no, number_format($booking->amount, 2));
                $event = 'approved';
        }

        return $this->push([(int) $booking->created_by], $title, $body, $booking, $event);
    }

    // ---------------------------------------------------------------- internal

    /**
     * @param  array $userIds
     * @return array|null  the FcmService result, or null when nobody to tell
     */
    private function push(array $userIds, $title, $body, PaymentBooking $booking, $event)
    {
        $userIds = array_values(array_unique(array_filter($userIds)));

        if (empty($userIds)) {
            return null;
        }

        try {
            $result = $this->fcm->sendToUsers($userIds, $title, $body, [
                // The app routes a tap using these.
                'type'         => 'payment_booking',
                'event'        => $event,
                'booking_id'   => $booking->id,
                'reference_no' => $booking->reference_no,
                'status'       => $booking->status,
            ]);

            Log::info('PaymentBookingNotifier: ' . $event, [
                'booking'   => $booking->reference_no,
                'recipients' => count($userIds),
                'result'    => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            // Never let a notification failure break the booking flow.
            Log::error('PaymentBookingNotifier failed', [
                'event'   => $event,
                'booking' => $booking->reference_no,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }
}
