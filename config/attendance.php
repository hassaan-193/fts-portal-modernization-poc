<?php

/**
 * QR Attendance Configuration
 * 
 * All office location settings are configured in .env file
 * This config file maps those environment variables for easy access
 */

return [
    'office_name' => env('OFFICE_LOCATION_NAME', 'MainOffice'),
    'office_latitude' => (float) env('OFFICE_LATITUDE', 25.2048),
    'office_longitude' => (float) env('OFFICE_LONGITUDE', 55.2708),
    'geofence_radius_meters' => (int) env('OFFICE_GEOFENCE_RADIUS_METERS', 500),
    // Set ATTENDANCE_GEOFENCE_ENABLED=false in .env to allow attendance from any location
    'geofence_enabled' => env('ATTENDANCE_GEOFENCE_ENABLED', true),
    'qr_code_token' => env('QR_CODE_TOKEN', 'local-dev-qr-token'),

    // User IDs excluded from attendance reports and summaries (e.g. system/admin accounts)
    'excluded_user_ids' => array_map('intval', array_filter(
        explode(',', env('ATTENDANCE_EXCLUDED_USER_IDS', '1,7,13,18,19,20'))
    )),

    // Monthly attendance summary email recipients (comma-separated in .env)
    'monthly_summary_recipients' => array_filter(
        explode(',', env('ATTENDANCE_MONTHLY_RECIPIENTS', ''))
    ),

    // Saturday working rules
    'saturday' => [
        'start_hour' => 9,
        'start_minute' => 0,        // 9:00 AM
        'end_hour' => 13,
        'end_minute' => 0,          // 1:00 PM
        'late_hour' => 9,
        'late_minute' => 0,         // 9:00 AM (late after 9:01 AM)
        'checkout_hour' => 12,
        'checkout_minute' => 30,    // 12:30 PM checkout allowed
    ],
];
