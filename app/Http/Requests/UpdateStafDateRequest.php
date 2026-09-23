<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStafDateRequest extends FormRequest
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
            'staff_id' => 'required|exists:staf_profile,id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom messages for validation errors
     */
    public function messages()
    {
        return [
            'staff_id.required' => 'Please select a staff member',
            'staff_id.exists' => 'Selected staff member does not exist',
            'start_date.required' => 'Leave start date is required',
            'start_date.date_format' => 'Leave start date must be in format YYYY-MM-DD',
            'end_date.date_format' => 'Leave end date must be in format YYYY-MM-DD',
            'end_date.after_or_equal' => 'Leave end date must be after or equal to start date',
        ];
    }
}
