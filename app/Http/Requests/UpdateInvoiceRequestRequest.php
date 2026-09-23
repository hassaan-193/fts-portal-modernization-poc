<?php

namespace App\Http\Requests;

use App\Rules\LpoInDuplicationCheck;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequestRequest extends FormRequest
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
            'quotation_id' => ['required', new LpoInDuplicationCheck($this->route('invoiceRequest'))],
            'product.0' => 'required',
        ];
    }
    public function messages(){
        return [
            'quotation_id.required' => 'Lpoin is required.'
        ];
    }
}
