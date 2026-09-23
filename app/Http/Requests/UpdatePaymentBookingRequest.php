<?php

namespace App\Http\Requests;

use App\Models\PaymentBooking;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for an edited payment booking.
 *
 * The booking type is fixed at creation — a cheque booking can never become a cash
 * one — so the rule set is resolved from the stored record rather than the posted
 * value, and the posted value is ignored by the service on update.
 */
class UpdatePaymentBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = PaymentBooking::rulesFor($this->bookingType());

        // Type is not editable, so it is neither required nor trusted here.
        unset($rules['booking_type']);

        return $rules;
    }

    public function attributes()
    {
        return [
            'payee'               => $this->bookingType() === PaymentBooking::TYPE_CASH ? 'paid to' : 'payee / beneficiary',
            'payment_against'     => 'payment against',
            'project_cost_centre' => 'project / cost centre',
            'purpose'             => 'purpose / description',
        ];
    }

    /**
     * The stored type, falling back to the posted one only when the record is
     * unavailable (it always is on the web route, which is keyed by id).
     */
    private function bookingType()
    {
        $booking = PaymentBooking::find($this->route('payment_booking') ?: $this->route('id'));

        return $booking ? $booking->booking_type : $this->input('booking_type');
    }
}
