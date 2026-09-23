<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Quotation;

class UpdateQuotationRequest extends FormRequest
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
            'company_id' => 'required',
            'ref_no' => 'required|unique:quotations,ref_no,'.$this->quotation,
            'amount' => 'required|numeric'
        ];
    }
}
