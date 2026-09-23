<?php

namespace App\Http\Requests;

use App\Models\PettyCash;
use Illuminate\Foundation\Http\FormRequest;

class CreatePettyCashExpenseRequest extends FormRequest
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
                'name' => 'required',
                'date' => 'nullable',
                'amount' => 'nullable',
                'category' => 'nullable',
        ];
    }
}
