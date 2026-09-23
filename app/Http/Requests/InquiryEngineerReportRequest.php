<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InquiryEngineerReport;

class InquiryEngineerReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return InquiryEngineerReport::$rules;
    }
}
