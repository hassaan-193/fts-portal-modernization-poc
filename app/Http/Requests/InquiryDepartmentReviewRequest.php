<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InquiryDepartmentReview;

class InquiryDepartmentReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return InquiryDepartmentReview::$rules;
    }
}
