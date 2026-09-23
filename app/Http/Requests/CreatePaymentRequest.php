<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment;

class CreatePaymentRequest extends FormRequest
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
        $payment_type = \App\Models\Lookup::find(request()->get('payment_type'));
        return [
            'date_time' => 'required',
            'payment_type' => 'required|numeric',
            'payment_no' => Rule::requiredIf(function () use ($payment_type) {
                return isset($payment_type) && $payment_type->name != 'Cash';
            }),
            'clearance_date' => Rule::requiredIf(function () use ($payment_type) {
                return isset($payment_type) &&$payment_type->name == 'Cheque';
            }),
            'bank_name' => Rule::requiredIf(function () use ($payment_type) {
                return isset($payment_type) && $payment_type->name == 'Credit Card';
            }),
            'invoices' => 'required_if:type,vendor|array|min:1',
            'invoices.*' => 'required_if:type,vendor|integer|exists:payment_invoices,id',
            'total_amount' => 'required:numeric',
            'expense_account' => 'required_if:type,general',
            'account_id' => 'required',
        ];
    }
}
