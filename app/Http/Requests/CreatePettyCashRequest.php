<?php

namespace App\Http\Requests;

use App\Models\PettyCash;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\CreatePettyCashRequest;

class CreatePettyCashRequest extends FormRequest
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
        // dd(request()->all());
        return [
                'account_id' => 'required',
                'project_id' => 'required_if:trans_type,==,Project',
                'user_id' => 'required_if:trans_type,==,Advance',
                'vendor_id' => 'required_unless:trans_type,Advance',
                'type' => 'required',
                'amount' => 'required|numeric',
        ];
    }
}
