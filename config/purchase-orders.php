<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Purchase Order Admin Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure which employees should receive WhatsApp notifications
    | when a purchase order is forwarded to admin in Step 2.
    |
    | Add employee IDs from the employees table here.
    */
    
    'admin_notification_employee_ids' => [
        // Add employee IDs here, e.g., [1, 2, 3]
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Department Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure email addresses for department reviewers who should receive
    | notifications when a new purchase order is created.
    |
    | Add email addresses here, e.g., ['reviewer1@example.com', 'reviewer2@example.com']
    */
    
    'department_reviewer_emails' => [
        // Add email addresses here for department reviewers
        // e.g., 'department@example.com', 'reviewer@example.com'
        'nida.rak@example.com', 'tauheed.rak@example.com'
    ],

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Admin Approval Notification Emails
    |--------------------------------------------------------------------------
    |
    | Configure email addresses for stakeholders who should receive
    | notifications when a purchase order is approved by admin.
    | These emails will receive the LPOUT details in addition to the vendor.
    |
    | Add email addresses here, e.g., ['admin@example.com', 'manager@example.com']
    */
    
    'admin_approval_notification_emails' => [
        // Add email addresses here for admin approval notifications
        // e.g., 'accounts@example.com', 'manager@example.com'
        'tauheed.rak@example.com','prasad.rak@example.com','coordinator.rak@example.com','accounts.rak@example.com','purchase.rak@example.com','kirankumar.rak@example.com','abdulhaak.rak@example.com'
        ,'talhamoin.rak@example.com'
        ,'hemanth.rak@example.com'
        ,'adam.rak@example.com'
    ],

    /*
    |--------------------------------------------------------------------------
    | LPO Number Starting Counter
    |--------------------------------------------------------------------------
    |
    | The LPO invoice number counter is GLOBAL: it increases across every
    | company and every month and never resets. The next counter is always
    | max(highest existing counter + 1, lpo_start_number), so this value is
    | the floor the sequence begins from.
    */

    'lpo_start_number' => 3470,

    /*
    |--------------------------------------------------------------------------
    | Company TRN
    |--------------------------------------------------------------------------
    |
    | Fire Technical Services' own TRN. This is the number printed on the LPO
    | and sent to the vendor - never the vendor's own TRN, which is stored on
    | the vendor profile as `vat_no` for internal reference only.
    */

    'company_trn' => '100317831400003',
];
