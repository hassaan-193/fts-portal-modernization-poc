<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'request_type' => 'required|in:general,project,maintenance,store',
            'quotation_id' => 'nullable|exists:quotations,id',
            'project_id' => 'nullable|exists:projects,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after:date',
            'items' => 'nullable|array',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.material_name' => 'nullable|string',
            'items.*.quantity' => 'nullable|numeric|min:1',
            'has_item_code' => 'nullable|boolean',
            'vendor_id' => 'nullable|exists:vendors,id',
            'name' => 'nullable|string',
            'trn_no' => 'nullable|string',
            'kindly_attn' => 'required|string',
            'lpout_date' => 'nullable|date',
            'payment_type' => 'nullable|in:Cash,Cheque',
            'cheque_date' => 'nullable|date|required_if:payment_type,Cheque',
            'lpo_payment_preference_option' => 'nullable|in:default,custom',
            'custom_payment_preference' => 'nullable|string',
            'pricing_mode' => 'nullable|in:unit,lump',
            'manual_total' => 'nullable|numeric|min:0',
            'revised_from_lpoout_id' => 'nullable|integer|exists:lpoouts,id',
        ];
    }
}
