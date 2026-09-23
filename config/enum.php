<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax-Value
    |--------------------------------------------------------------------------
    |
    */
    'tax_rate' => 0.05,

    /*
    |--------------------------------------------------------------------------
    | Request-Form-Status
    |--------------------------------------------------------------------------
    |
    */
    'request_form_status' => [
        0 => 'Pending',
        1 => 'Approve',
        2 => 'Reject',
        3 => 'Hold',
    ],
    /*
    |--------------------------------------------------------------------------
    | Expirey-Document-Types
    |--------------------------------------------------------------------------
    |
    */
    'expirey_document_types' => [
        'Vehicle Expiry' => 'Vehicle Expiry',
        'Life Insurance Expiry' => 'Life Insurance Expiry',
        'Health Insurance' => 'Health Insurance',
        'Licenses Expiry' => 'Licenses Expiry',
        'Undertaking Letter' => 'Undertaking Letter',
        'Other' => 'Other'
    ],
    /*
    |--------------------------------------------------------------------------
    | Document-Types
    |--------------------------------------------------------------------------
    |
    */
    'document_types' => [
        'passport' => 'Passport',
        'visa' => 'Visa',
        'contract' => 'Contract',
        'emirates_id' => 'Emirates ID',
        'insurance' => 'Insurance',
        'driving_license'=>'Driving License',
        'jalaa_house_lease'=>'Eng. Jalaa House Lease Agreement',
    ],
    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    */
    'currency' => [
        'AED' => 'AED',
        'USD' => 'USD',
        'INR' => 'INR',
    ],
    /*
    |--------------------------------------------------------------------------
    | Total Staff Leaves
    |--------------------------------------------------------------------------
    |
    */
    'total_staff_leaves' => 26,

    /*
    |--------------------------------------------------------------------------
    | Drawing Received - Type of Work
    |--------------------------------------------------------------------------
    |
    */
    'drawing_type_of_work' => [
        'Municipality Approval' => 'Municipality Approval',
        'Rakez Approval' => 'Rakez Approval',
        'Civil Defence Approval - Initial' => 'Civil Defence Approval - Initial',
        'Civil Defence Approval - Shop Drawing' => 'Civil Defence Approval - Shop Drawing',
        'Civil Defence Approval - Decor' => 'Civil Defence Approval - Decor',
        'Civil Defence Approval - LPG' => 'Civil Defence Approval - LPG',
        'Civil Defence Approval - Suppression' => 'Civil Defence Approval - Suppression',
    ],

    /*
    |--------------------------------------------------------------------------
    | Drawing Received - Status
    |--------------------------------------------------------------------------
    |
    */
    'drawing_status' => [
        'Under Review' => 'Under Review',
        'Comments Received' => 'Comments Received',
        'Revised & Resubmitted' => 'Revised & Resubmitted',
        'Approved' => 'Approved',
    ],

    /*
    |--------------------------------------------------------------------------
    | Drawing Received - Contribution Types
    |--------------------------------------------------------------------------
    |
    */
    'drawing_contribution_types' => [
        'Initial Submission' => 'Initial Submission',
        'Status Update' => 'Status Update',
        'Revision Submitted' => 'Revision Submitted',
        'Comment Added' => 'Comment Added',
        'Attachment Added' => 'Attachment Added',
        'Approved' => 'Approved',
        'Rejected' => 'Rejected',
    ],
];
