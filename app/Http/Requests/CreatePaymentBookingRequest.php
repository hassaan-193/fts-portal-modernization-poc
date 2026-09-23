<?php

namespace App\Http\Requests;

use App\Models\PaymentBooking;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for a new payment booking.
 *
 * The rule set depends on which radio the accountant picked, so it is resolved
 * from the posted booking_type via PaymentBooking::rulesFor() — the same method
 * the mobile API validates against.
 */
class CreatePaymentBookingRequest extends FormRequest
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
        return PaymentBooking::rulesFor($this->input('booking_type'));
    }

    public function attributes()
    {
        return [
            'booking_type'        => 'booking type',
            'payee'               => $this->input('booking_type') === PaymentBooking::TYPE_CASH ? 'paid to' : 'payee / beneficiary',
            'payment_against'     => 'payment against',
            'project_cost_centre' => 'project / cost centre',
            'purpose'             => 'purpose / description',
        ];
    }
}
