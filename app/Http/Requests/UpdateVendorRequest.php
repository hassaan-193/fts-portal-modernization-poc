<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
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
        return [
            'name' => 'required',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email',
            'payment_preference' => 'required|in:cod,pdc',
            'pdc_number_of_days' => 'required_if:payment_preference,pdc|nullable|integer|min:1',
            'pdc_payment_option' => 'required_if:payment_preference,pdc|nullable|in:on_delivery_amount,on_payment_release_amount',
            // 'email' => Rule::unique('vendors')->ignore(request()->segment(2)),
        ];
    }
}
