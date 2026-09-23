<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lpoout;

class UpdateLpooutRequest extends FormRequest
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
        $type = \App\Models\LpoOutType::whereName('Project')->first()->id;
        
        return [
            'name' => 'required',
            'lpo_out_type_id' => 'required',
            'project_id' => 'required_if:lpo_out_type_id,==,'.$type,
            'vendor_id' => 'required',
            'amount' => 'required|numeric',
            'trn_no' => 'nullable|string|max:255',
            'pricing_mode' => 'nullable|in:unit,lump',
            'has_item_code' => 'nullable|boolean',
            'items.*.item_code' => 'nullable|string|max:255',
            'kindly_attn' => 'nullable|string|max:255',
            'payment_type' => 'nullable|in:Cash,Cheque',
            'cheque_date' => 'required_if:payment_type,Cheque|nullable|date',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string',
            'items.*.unit' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.unit_price' => 'nullable|numeric',
            'items.*.total' => 'nullable|numeric',
            'lpo_payment_preference_option' => 'required|in:default,custom',
            'lpo_payment_preference' => 'required_if:lpo_payment_preference_option,custom|nullable|in:cod,pdc',
            'lpo_pdc_number_of_days' => 'required_if:lpo_payment_preference_option,custom|required_if:lpo_payment_preference,pdc|nullable|integer|min:1',
            'lpo_pdc_payment_option' => 'required_if:lpo_payment_preference_option,custom|required_if:lpo_payment_preference,pdc|nullable|in:on_delivery_amount,on_payment_release_amount',
        ];
    }

        /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cheque_date.required_if' => 'Cheque date is required when payment type is Cheque.',
        ];
    }
}
