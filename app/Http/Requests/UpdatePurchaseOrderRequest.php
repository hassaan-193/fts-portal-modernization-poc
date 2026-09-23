<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quotation_id' => 'nullable|exists:quotations,id',
            'date' => 'required|date',
            'kindly_attn' => 'required|string',
            'items' => 'nullable|array',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.material_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'has_item_code' => 'nullable|boolean',
        ];
    }
}
