<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // LPOUT Items (from the Items for Local Purchase Outbound table)
            'lpout_items' => 'nullable|array',
            'lpout_items.*.item_code' => 'nullable|string|max:255',
            'lpout_items.*.description' => 'nullable|string',
            'lpout_items.*.unit' => 'nullable|string',
            'lpout_items.*.qty' => 'nullable|numeric|min:0',
            'lpout_items.*.unit_price' => 'nullable|numeric|min:0',
            'lpout_items.*.total' => 'nullable|numeric|min:0',
            
            'department_notes' => 'nullable|string',

            'vat' => 'nullable|boolean',
            'terms' => 'nullable|string',

            // Pricing mode: 'unit' = per-unit (qty x unit_price), 'lump' = single batch total
            'pricing_mode' => 'nullable|in:unit,lump',
            'manual_total' => 'nullable|numeric|min:0',

            // Optional ITEM CODE column, independent of pricing mode
            'has_item_code' => 'nullable|boolean',
        ];
    }
}
