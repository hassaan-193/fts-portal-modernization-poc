<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
            // 'email' => 'required|unique:companies',
            'email' => Rule::unique('companies')->ignore(request()->segment(2)),
            'billing_contact_person' => 'required',
            'billing_pob' => 'required',
            'billing_email' => 'required'
        ];
    }
}
