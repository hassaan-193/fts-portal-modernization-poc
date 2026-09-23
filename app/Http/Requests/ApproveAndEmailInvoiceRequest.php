<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAndEmailInvoiceRequest extends FormRequest
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
            'invoice_request_id' => 'required|exists:invoice_requests,id',
            'letterhead_type' => 'required|in:fts,experts,ftsits',
            'cc_emails' => 'nullable|string',
            'fixed_cc_emails' => 'nullable|array',
            'fixed_cc_emails.*' => 'email',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'invoice_request_id.required' => 'Invoice request is required.',
            'invoice_request_id.exists' => 'Invoice request not found.',
            'letterhead_type.required' => 'Please select a letterhead template.',
            'letterhead_type.in' => 'Invalid letterhead template selected.',
        ];
    }

    /**
     * Parse and validate CC emails
     *
     * @return array
     */
    public function getCcEmails()
    {
        $ccString = $this->input('cc_emails', '');
        if (!$ccString) {
            return [];
        }

        // Split by comma and trim
        $emails = array_map('trim', explode(',', $ccString));
        
        // Filter out empty strings and validate
        $validEmails = [];
        $invalidEmails = [];

        foreach ($emails as $email) {
            if (!empty($email)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validEmails[] = $email;
                } else {
                    $invalidEmails[] = $email;
                }
            }
        }

        // Store invalid emails for error reporting
        if (!empty($invalidEmails)) {
            $this->merge(['invalid_cc_emails' => $invalidEmails]);
        }

        return $validEmails;
    }

    /**
     * Get fixed CC emails from checkboxes
     *
     * @return array
     */
    public function getFixedCcEmails()
    {
        $fixedEmails = $this->input('fixed_cc_emails', []);
        return is_array($fixedEmails) ? $fixedEmails : [];
    }

    /**
     * Get merged CC emails (manual + fixed checkboxes)
     *
     * @return array
     */
    public function getMergedCcEmails()
    {
        $manual = $this->getCcEmails();
        $fixed = $this->getFixedCcEmails();
        
        // Merge and remove duplicates
        $merged = array_unique(array_merge($manual, $fixed));
        return array_filter($merged, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }
}

