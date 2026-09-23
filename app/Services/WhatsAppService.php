<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class WhatsAppService
{
    protected $apiKey;
    protected $maxRetries = 3;
    protected $retryDelay = 2;
    protected $messageDelay = 5;

    public function __construct()
    {
        $this->apiKey = config('services.wasender.api_key');
        $this->messageDelay = config('services.wasender.message_delay', 5);
    }

    /**
     * Check if error is transient (retryable)
     */
    private function isTransientError(\Exception $e): bool
    {
        $message = $e->getMessage();
        return stripos($message, 'cURL error') !== false ||
               stripos($message, 'Connection') !== false ||
               stripos($message, 'timeout') !== false ||
               stripos($message, 'timed out') !== false;
    }

    /**
     * Send message with retry logic and exponential backoff
     */
    private function sendMessageWithRetry($mobile, $message, $retryCount = 0): array
    {
        $mobile = preg_replace('/^\+/', '', $mobile);

        try {
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(60)
                ->post('https://www.wasenderapi.com/api/send-message', [
                    'to' => $mobile,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', ['mobile' => $mobile, 'attempt' => $retryCount + 1]);
                return ['success' => true, 'message' => 'Sent'];
            }

            // Retry on rate limit (429)
            if ($response->status() === 429 && $retryCount < $this->maxRetries) {
                Log::warning('Rate limited, retrying...', ['mobile' => $mobile, 'attempt' => $retryCount + 1]);
                sleep($this->retryDelay * ($retryCount + 1));
                return $this->sendMessageWithRetry($mobile, $message, $retryCount + 1);
            }

            Log::warning('Failed to send WhatsApp', ['mobile' => $mobile, 'status' => $response->status(), 'attempt' => $retryCount + 1]);
            return ['success' => false, 'message' => 'HTTP ' . $response->status(), 'permanent' => true];
        } catch (\Exception $e) {
            $isTransient = $this->isTransientError($e);

            if ($isTransient && $retryCount < $this->maxRetries) {
                Log::warning('Transient error, retrying...', ['mobile' => $mobile, 'attempt' => $retryCount + 1, 'error' => $e->getMessage()]);
                sleep($this->retryDelay * ($retryCount + 1));
                return $this->sendMessageWithRetry($mobile, $message, $retryCount + 1);
            }

            Log::error('WhatsApp send failed', ['mobile' => $mobile, 'error' => $e->getMessage(), 'attempt' => $retryCount + 1, 'permanent' => !$isTransient]);
            return ['success' => false, 'message' => $e->getMessage(), 'permanent' => !$isTransient];
        }
    }

    /**
     * Get users who should always receive inquiry notifications (e.g., project owners)
     */
    private function getAlwaysNotifyUsers(): \Illuminate\Database\Eloquent\Collection
    {
        $userIds = env('INQUIRY_ALWAYS_NOTIFY_USER_IDS', '');
        if (empty($userIds)) {
            return collect([]);
        }

        $idArray = array_filter(array_map('trim', explode(',', $userIds)));
        if (empty($idArray)) {
            return collect([]);
        }

        return \App\User::whereIn('id', $idArray)->get();
    }

    public function sendMessage($mobile, $title, $content, $issued_by, $issued_at, $letter = null)
    {
        // Validate phone number
        if (!$mobile) {
            Log::warning('WhatsApp sendMessage called with empty mobile number');
            throw new \Exception('Mobile number cannot be empty');
        }
        
        // Remove + if exists
        $mobile = preg_replace('/^\+/', '', $mobile);

        $message = <<<MSG
        🚨 Warning Notice: {$title} 🚨

        Details of the Warning:
        {$content}

        Issued By: {$issued_by}
        Issued At: {$issued_at}

        Please take the necessary action at your earliest convenience. If you have any questions, don't hesitate to contact us.
        MSG;

        try {
            // Build payload
            $payload = [
                'to' => $mobile,
                'text' => $message,
            ];

            Log::info('WhatsApp sendMessage - Starting', [
                'mobile' => $mobile,
                'letter_id' => $letter ? $letter->id : 'null',
                'has_letter' => $letter ? 'yes' : 'no'
            ]);

            // Attach media files if letter has media using public URLs
            if ($letter && method_exists($letter, 'getMedia')) {
                Log::info('WhatsApp sendMessage - Letter has getMedia method');
                
                $mediaItems = $letter->getMedia();
                
                Log::info('WhatsApp sendMessage - Media items count', [
                    'count' => $mediaItems->count()
                ]);
                
                if (!$mediaItems->isEmpty()) {
                    Log::info('WhatsApp sendMessage - Media items found', [
                        'media_count' => $mediaItems->count()
                    ]);
                    
                    // Process first media file
                    $media = $mediaItems->first();
                    
                    Log::info('WhatsApp sendMessage - Processing media file', [
                        'file_name' => $media->name,
                        'file_size' => $media->size,
                        'mime_type' => $media->mime_type
                    ]);
                    
                    try {
                        // Get public URL of the media file
                        $publicUrl = $media->getFullUrl();
                        
                        if ($publicUrl) {
                            $payload['documentUrl'] = $publicUrl;
                            
                            Log::info('Media file URL added to payload', [
                                'file_name' => $media->name,
                                'file_size' => $media->size,
                                'mime_type' => $media->mime_type,
                                'public_url' => $publicUrl
                            ]);
                        } else {
                            Log::warning('Failed to get public URL for media', [
                                'file_name' => $media->name
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to process media file', [
                            'file_name' => $media->name,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    Log::info('WhatsApp sendMessage - No media items found');
                }
            } else {
                Log::info('WhatsApp sendMessage - Letter is null or missing getMedia', [
                    'letter_exists' => $letter ? 'yes' : 'no',
                    'has_method' => $letter && method_exists($letter, 'getMedia') ? 'yes' : 'no'
                ]);
            }

            Log::info('WhatsApp sendMessage - Final payload', [
                'payload_keys' => array_keys($payload),
                'has_documentUrl' => isset($payload['documentUrl']) ? 'yes' : 'no'
            ]);

            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->post('https://www.wasenderapi.com/api/send-message', $payload);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'mobile' => $mobile,
                    'response' => $response->json()
                ]);
                return true;
            }

            throw new \Exception(
                'Failed to send WhatsApp message. Status: ' . $response->status() . ' Response: ' . $response->body()
            );
        } catch (\Exception $e) {
            Log::error('WhatsApp sending exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            throw $e;
        }
    }

    public function sendBirthdayWishes($staffProfile)
    {
        if (!$staffProfile->mobile_no) {
            Log::warning('Staff has no mobile number for birthday wishes', [
                'staff_id' => $staffProfile->id,
                'staff_name' => $staffProfile->name,
                'timestamp' => now()
            ]);
            return false;
        }

        $mobile = trim($staffProfile->mobile_no);
        
        // Validate mobile number format (at least 7 digits)
        if (!preg_match('/\d{7,}/', $mobile)) {
            Log::warning('Staff has invalid mobile number format', [
                'staff_id' => $staffProfile->id,
                'staff_name' => $staffProfile->name,
                'mobile_no' => $mobile,
                'timestamp' => now()
            ]);
            return false;
        }
        
        $mobile = preg_replace('/^\+/', '', $mobile);
        $staffName = $staffProfile->name . ' ' . $staffProfile->last_name;

        $message = <<<MSG
        🎉 Happy Birthday! 🎉

        Dear {$staffName},

        Wishing you a wonderful birthday filled with joy, health, and happiness!

        We appreciate your hard work and dedication. Have a fantastic day!

        Best wishes,
        Management Team
        MSG;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post('https://www.wasenderapi.com/api/send-message', [
                'to' => $mobile,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('Birthday wishes sent successfully', [
                    'staff_id' => $staffProfile->id,
                    'staff_name' => $staffName,
                    'mobile' => $mobile
                ]);
                return true;
            }

            throw new \Exception(
                'Failed to send birthday wishes. Status: ' . $response->status() . ' Response: ' . $response->body()
            );
        } catch (\Exception $e) {
            Log::error('Birthday wishes sending exception', [
                'staff_id' => $staffProfile->id,
                'staff_name' => $staffName,
                'mobile' => $mobile,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            return false;
        }
    }

    public function sendBirthdayNotificationsToManagers($staffsWithBirthday, $managerPhones)
    {
        $results = [];

        // Convert manager phones to array if string
        if (is_string($managerPhones)) {
            $managerPhones = array_filter(array_map('trim', explode(',', $managerPhones)));
        }

        if (empty($managerPhones)) {
            Log::warning('No manager phone numbers provided for birthday notifications');
            return [
                'success' => false,
                'message' => 'No manager phone numbers configured',
                'sent' => 0,
                'failed' => 0
            ];
        }

        if (empty($staffsWithBirthday)) {
            Log::info('No staff with birthdays today');
            return [
                'success' => true,
                'message' => 'No staff with birthdays today',
                'sent' => 0,
                'failed' => 0
            ];
        }

        // Build staff list for message
        $staffList = '';
        foreach ($staffsWithBirthday as $staff) {
            $staffList .= '• ' . $staff->name . ' ' . $staff->last_name . "\n";
        }

        $staffCount = count($staffsWithBirthday);
        $message = <<<MSG
        🎂 Staff Birthday Notification 🎂

        Today, we have {$staffCount} staff member(s) celebrating their birthday:

        $staffList

        Please take a moment to wish them and make their day special!

        Thank you!
        MSG;

        $sentCount = 0;
        $failedCount = 0;

        foreach ($managerPhones as $index => $managerPhone) {
            try {
                $mobile = trim($managerPhone);
                
                // Skip empty phone numbers
                if (!$mobile) {
                    $failedCount++;
                    Log::warning('Empty manager phone number encountered in birthday notifications');
                    continue;
                }
                
                // Validate phone number format (at least 7 digits)
                if (!preg_match('/\d{7,}/', $mobile)) {
                    $failedCount++;
                    Log::warning('Invalid manager phone number format for birthday notification', [
                        'phone' => $mobile,
                        'timestamp' => now()
                    ]);
                    continue;
                }
                
                $mobile = preg_replace('/^\+/', '', $mobile);

                // Add delay between messages
                if ($index > 0) {
                    sleep(5);
                }

                Log::info('Sending birthday notification to manager', [
                    'manager_phone' => $mobile,
                    'staff_count' => $staffCount
                ]);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->post('https://www.wasenderapi.com/api/send-message', [
                    'to' => $mobile,
                    'text' => $message,
                ]);

                if ($response->successful()) {
                    Log::info('Birthday notification sent to manager successfully', [
                        'manager_phone' => $mobile,
                        'staff_count' => $staffCount
                    ]);
                    $sentCount++;
                } else {
                    $failedCount++;
                    Log::warning('Failed to send birthday notification to manager', [
                        'manager_phone' => $mobile,
                        'status_code' => $response->status(),
                        'response' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Exception sending birthday notification to manager', [
                    'manager_phone' => $managerPhone,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $results = [
            'success' => $failedCount === 0,
            'staff_with_birthdays' => $staffCount,
            'managers_notified' => count($managerPhones),
            'sent' => $sentCount,
            'failed' => $failedCount,
            'message' => "Sent to {$sentCount} manager(s), {$failedCount} failed"
        ];

        Log::info('Birthday notifications to managers completed', $results);

        return $results;
    }

    public function sendAttendanceApprovalNotification($mobile, $staffName)
    {
        // Remove + if exists
        $mobile = preg_replace('/^\+/', '', $mobile);

        $message = <<<MSG
        ✅ Attendance Approval

        Hi {$staffName},

        This is a warning for being absent from work without permission from your Engineer/Supervisor.
        Being absent without approval is not allowed and affects the work of the team. All employees must get approval before taking leave. 
        This letter is a formal warning. If this happens again, disciplinary action may be taken. IMPORTANT NOTE: If you are absent for two (2) days in a row, you must submit a written explanation or a medical certificate if you are sick. If you do not submit this, it may lead to disciplinary action or salary deduction. Please follow the attendance rules going forward.

        Thank you!
        MSG;

        try {
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->post('https://www.wasenderapi.com/api/send-message', [
                    'to' => $mobile,
                    'text' => $message,
                ]);


            if ($response->successful()) {
                return true;
            }

            throw new \Exception(
                'Failed to send attendance approval WhatsApp message. Status: ' . $response->status() . ' Response: ' . $response->body()
            );
        } catch (\Exception $e) {
            Log::error('Attendance approval WhatsApp sending exception', [
                'mobile' => $mobile,
                'staff_name' => $staffName,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            throw $e;
        }
    }

    public function sendAttendanceSummary($presentUsers, $lateUsers, $absentUsers)
    {
        // Hardcoded WhatsApp numbers for morning attendance notifications
        $notificationNumbers = [
            '+971500000001',
            '+971500000002',
            '+971500000003',
        ];



        $presentCount = $presentUsers->count();
        $lateCount = $lateUsers->count();
        $absentCount = $absentUsers->count();

        // Build absent list
        $absentList = '';
        if ($absentCount > 0) {
            foreach ($absentUsers as $user) {
                $absentList .= $user->name . "\n";
            }
        } else {
            $absentList = "None\n";
        }

        // Build late list with clock-in times
        $lateList = '';
        if ($lateCount > 0) {
            foreach ($lateUsers as $item) {
                $user = $item['user'];
                $clockInTime = $item['clock_in_time']->format('H:i');
                $lateList .= $user->name . " " . $clockInTime . "\n";
            }
        } else {
            $lateList = "None\n";
        }

        // Build present list with clock-in times
        $presentList = '';
        if ($presentCount > 0) {
            foreach ($presentUsers as $item) {
                $user = $item['user'];
                $clockInTime = $item['clock_in_time']->format('H:i');
                $presentList .= $user->name . " " . $clockInTime . "\n";
            }
        } else {
            $presentList = "None\n";
        }

        $message = <<<MSG
        📋 *Morning Attendance Summary* 📋
        *Time: 8:30 AM*

        ❌ *Absent ($absentCount)*
        $absentList
        ⏰ *Late ($lateCount)*
        $lateList
        ✅ *Present ($presentCount)*
        $presentList
        ---
        **
        MSG;



        $successCount = 0;
        $failureCount = 0;

        foreach ($notificationNumbers as $index => $number) {
            try {
                // Add delay between messages (5 seconds minimum based on API rate limit)
                if ($index > 0) {
                    sleep(5);
                }

                $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post('https://www.wasenderapi.com/api/send-message', [
                        'to' => $number,
                        'text' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning('Failed to send attendance summary', [
                        'number' => $number,
                        'status_code' => $response->status(),
                        'response' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Exception sending attendance summary', [
                    'number' => $number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $failureCount === 0,
            'sent' => $successCount,
            'failed' => $failureCount,
            'total' => count($notificationNumbers),
        ];
    }

    public function sendAttendanceSummaryEmail($presentUsers, $lateUsers, $absentUsers)
    {
        $notificationEmails = config('attendance.monthly_summary_recipients', []);

        $presentCount = $presentUsers->count();
        $lateCount = $lateUsers->count();
        $absentCount = $absentUsers->count();

        // Build absent list
        $absentList = '';
        if ($absentCount > 0) {
            foreach ($absentUsers as $user) {
                $absentList .= $user->name . "\n";
            }
        } else {
            $absentList = "None\n";
        }

        // Build late list with clock-in times
        $lateList = '';
        if ($lateCount > 0) {
            foreach ($lateUsers as $item) {
                $user = $item['user'];
                $clockInTime = $item['clock_in_time']->format('H:i');
                $lateList .= $user->name . " " . $clockInTime . "\n";
            }
        } else {
            $lateList = "None\n";
        }

        // Build present list with clock-in times
        $presentList = '';
        if ($presentCount > 0) {
            foreach ($presentUsers as $item) {
                $user = $item['user'];
                $clockInTime = $item['clock_in_time']->format('H:i');
                $presentList .= $user->name . " " . $clockInTime . "\n";
            }
        } else {
            $presentList = "None\n";
        }

        $emailMessage = <<<MSG
        Morning Attendance Summary
        ===========================
        Time: 8:30 AM
        Date: {{DATE}}

        ABSENT ($absentCount):
        $absentList

        LATE ($lateCount):
        $lateList

        PRESENT ($presentCount):
        $presentList

        ---
        **
        MSG;

        $emailMessage = str_replace('{{DATE}}', now()->format('Y-m-d'), $emailMessage);



        try {
            Mail::raw($emailMessage, function ($mail) use ($notificationEmails) {
                $mail->from(
                    config('mail.from.address'),
                    config('mail.from.name')
                )
                ->to($notificationEmails)
                ->subject('Morning Attendance Summary | ' . now()->format('Y-m-d'));
            });



            return [
                'success' => true,
                'sent' => count($notificationEmails),
                'message' => 'Email sent successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Exception sending attendance summary email', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send new inquiry notification to department staff (queued)
     */
    public function sendNewInquiryNotification($inquiry, $useQueue = true)
    {
        if ($useQueue) {
            Queue::push(function () use ($inquiry) {
                $this->sendNewInquiryNotificationNow($inquiry);
            });
            Log::info('Inquiry notification queued', ['inquiry_id' => $inquiry->id]);
            return ['success' => true, 'message' => 'Queued', 'queued' => true];
        }
        return $this->sendNewInquiryNotificationNow($inquiry);
    }

    private function sendNewInquiryNotificationNow($inquiry)
    {
        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;
        $failedUsers = [];

        // Get StafProfile IDs from .env configuration
        $alwaysNotifyStaffIds = env('INQUIRY_NOTIFY_STAFF_IDS', '12,10');
        $staffIds = array_filter(array_map('trim', explode(',', $alwaysNotifyStaffIds)));
        
        // Add AMC-specific staff if inquiry type is AMC
        if ($inquiry->inquiry_type === 'AMC') {
            $amcOnlyStaffIds = env('INQUIRY_NOTIFY_STAFF_IDS_AMC_ONLY', '13');
            $amcIds = array_filter(array_map('trim', explode(',', $amcOnlyStaffIds)));
            $staffIds = array_merge($staffIds, $amcIds);
        }

        if (empty($staffIds)) {
            Log::warning('No staff IDs configured for inquiry notification', ['inquiry_id' => $inquiry->id]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        // Get staff profiles by their IDs
        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIds)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found for inquiry notification', ['configured_staff_ids' => $staffIds, 'inquiry_id' => $inquiry->id]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        foreach ($staffProfiles as $index => $staffProfile) {
            try {
                if (!$staffProfile->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff profile has no mobile number', ['staff_id' => $staffProfile->id, 'staff_name' => $staffProfile->name, 'inquiry_id' => $inquiry->id]);
                    continue;
                }

                if ($index > 0) sleep($this->messageDelay);

                $message = "📌 *New Inquiry Received*\n\nInquiry No: {$inquiry->inquiry_no}\nClient: {$inquiry->client_name}\nType: {$inquiry->inquiry_type}\nDepartment: {$inquiry->assigned_department}\nPriority: {$inquiry->priority}\n\nPlease review and take necessary action.";

                $result = $this->sendMessageWithRetry($staffProfile->mobile_no, $message);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                    $failedUsers[] = ['staff_name' => $staffProfile->name, 'reason' => $result['message'], 'permanent' => $result['permanent'] ?? false];
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Inquiry notification error', ['staff_id' => $staffProfile->id, 'inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
                $failedUsers[] = ['staff_name' => $staffProfile->name, 'reason' => $e->getMessage(), 'permanent' => false];
            }
        }

        Log::info('Inquiry notification batch completed', ['sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total_staff' => count($staffProfiles)]);
        return ['success' => $failureCount === 0, 'sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total' => count($staffProfiles), 'failed_users' => $failedUsers];
    }

    /**
     * Send quotation created notification to sales and department (queued)
     */
    public function sendQuotationCreatedNotification($inquiry, $quotation, $useQueue = true)
    {
        if ($useQueue) {
            Queue::push(function () use ($inquiry, $quotation) {
                $this->sendQuotationCreatedNotificationNow($inquiry, $quotation);
            });
            Log::info('Quotation notification queued', ['inquiry_id' => $inquiry->id]);
            return ['success' => true, 'message' => 'Queued', 'queued' => true];
        }
        return $this->sendQuotationCreatedNotificationNow($inquiry, $quotation);
    }

    private function sendQuotationCreatedNotificationNow($inquiry, $quotation)
    {
        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;
        $failedUsers = [];

        // Get specific sales staff by ID from .env configuration
        $salesStaffIds = env('QUOTATION_NOTIFY_SALES_STAFF_IDS', '149');
        $staffIdArray = array_filter(array_map('trim', explode(',', $salesStaffIds)));
        
        if (empty($staffIdArray)) {
            Log::warning('No staff IDs configured in QUOTATION_NOTIFY_SALES_STAFF_IDS env variable', ['inquiry_id' => $inquiry->id]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        // Get staff profiles by their IDs
        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIdArray)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found for quotation notification', ['staff_ids' => $staffIdArray, 'inquiry_id' => $inquiry->id]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        foreach ($staffProfiles as $index => $staffProfile) {
            try {
                if (!$staffProfile->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff profile has no mobile number for quotation', ['staff_id' => $staffProfile->id, 'staff_name' => $staffProfile->name]);
                    continue;
                }

                if ($index > 0) sleep($this->messageDelay);

                $amount = $quotation->quotation_amount ? 'AED ' . number_format($quotation->quotation_amount, 2) : 'N/A';
                $message = "✅ *Quotation Created*\n\nInquiry No: {$inquiry->inquiry_no}\nClient: {$inquiry->client_name}\nAmount: {$amount}\nCreated By: {$quotation->creator->name}\n\nPlease review and proceed with follow-up.";

                $result = $this->sendMessageWithRetry($staffProfile->mobile_no, $message);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                    $failedUsers[] = ['staff_name' => $staffProfile->name, 'reason' => $result['message'], 'permanent' => $result['permanent'] ?? false];
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Quotation notification error', ['staff_id' => $staffProfile->id, 'inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
                $failedUsers[] = ['staff_name' => $staffProfile->name, 'reason' => $e->getMessage(), 'permanent' => false];
            }
        }

        Log::info('Quotation notification batch completed', ['sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total_staff' => count($staffProfiles)]);
        return ['success' => $failureCount === 0, 'sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total' => count($staffProfiles), 'failed_users' => $failedUsers];
    }

    /**
     * Send engineer assignment notification (queued)
     */
    public function sendEngineerAssignmentNotification($inquiry, $engineerId, $useQueue = true)
    {
        if ($useQueue) {
            Queue::push(function () use ($inquiry, $engineerId) {
                $this->sendEngineerAssignmentNotificationNow($inquiry, $engineerId);
            });
            Log::info('Engineer assignment notification queued', ['inquiry_id' => $inquiry->id, 'engineer_id' => $engineerId]);
            return ['success' => true, 'message' => 'Queued', 'queued' => true];
        }
        return $this->sendEngineerAssignmentNotificationNow($inquiry, $engineerId);
    }

    private function sendEngineerAssignmentNotificationNow($inquiry, $engineerId)
    {
        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;
        $failedUsers = [];

        try {
            // Get the assigned engineer + always-notify users
            $engineer = \App\User::findOrFail($engineerId);
            $alwaysNotifyUsers = $this->getAlwaysNotifyUsers();
            $users = collect([$engineer])->merge($alwaysNotifyUsers)->unique('id');

            $visitDate = optional($inquiry->departmentReview)->proposed_visit_date;
            $visitDateStr = $visitDate ? $visitDate->format('Y-m-d') : 'To be confirmed';

            foreach ($users as $index => $user) {
                try {
                    $staffProfile = \App\Models\StafProfile::where('name', 'like', '%' . trim(strtoupper($user->name)) . '%')
                        ->orWhere('name', 'like', '%' . $user->name . '%')->first();

                    if (!$staffProfile || !$staffProfile->mobile_no) {
                        $notSentCount++;
                        Log::warning('Staff profile/mobile not found for engineer notification', ['user_id' => $user->id, 'user_name' => $user->name]);
                        continue;
                    }

                    if ($index > 0) sleep($this->messageDelay);

                    $message = "👷 *Site Visit Assignment*\n\nInquiry No: {$inquiry->inquiry_no}\nClient: {$inquiry->client_name}\nType: {$inquiry->inquiry_type}\nLocation: {$inquiry->location}\nProposed Date: {$visitDateStr}\n\nPlease confirm and submit report after visit.";

                    $result = $this->sendMessageWithRetry($staffProfile->mobile_no, $message);

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failureCount++;
                        $failedUsers[] = ['user_name' => $user->name, 'reason' => $result['message'], 'permanent' => $result['permanent'] ?? false];
                    }
                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error('Error sending engineer notification', ['user_id' => $user->id, 'inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
                    $failedUsers[] = ['user_name' => $user->name, 'reason' => $e->getMessage(), 'permanent' => false];
                }
            }

            Log::info('Engineer assignment notification batch completed', ['sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total_users' => count($users)]);
            return ['success' => $failureCount === 0, 'sent' => $successCount, 'failed' => $failureCount, 'not_sent' => $notSentCount, 'total' => count($users), 'failed_users' => $failedUsers];
        } catch (\Exception $e) {
            Log::error('Engineer assignment notification error', ['engineer_id' => $engineerId, 'inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'sent' => 0, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send leave request notification to designated staff (labor or office staff form)
     *
     * @param \Illuminate\Database\Eloquent\Model $request  The created LaborRequest or StaffRequest
     * @param \App\Models\StafProfile             $requester The staff member who submitted the request
     * @param string                              $formType  'labor' or 'staff'
     */
    public function sendLeaveRequestNotification($request, $requester, string $formType): array
    {
        $envKey = $formType === 'labor'
            ? 'LEAVE_REQUEST_LABOR_NOTIFY_STAFF_IDS'
            : 'LEAVE_REQUEST_STAFF_NOTIFY_STAFF_IDS';

        $defaultIds = $formType === 'labor' ? '12,60,9' : '15,60,12';

        $rawIds = env($envKey, $defaultIds);
        $staffIds = array_filter(array_map('trim', explode(',', $rawIds)));

        if (empty($staffIds)) {
            Log::warning("No staff IDs configured in {$envKey} for leave request notification", [
                'request_id' => $request->id,
                'form_type'  => $formType,
            ]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIds)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning("No staff profiles found for {$envKey}", [
                'configured_ids' => $staffIds,
                'request_id'     => $request->id,
            ]);
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'not_sent' => 0, 'total' => 0];
        }

        $requesterName = trim($requester->name . ' ' . $requester->last_name);
        $requestType   = $request->type ?? 'N/A';
        $startDate     = $request->start_date ?? 'N/A';
        $endDate       = $request->end_date   ?? 'N/A';
        $note          = $request->note       ?? '';
        $formLabel     = $formType === 'labor' ? 'Labor' : 'Office Staff';

        $messageLines = [
            "📋 *New {$formLabel} Leave Request*",
            "",
            "Submitted By: {$requesterName}",
            "Type: {$requestType}",
            "From: {$startDate}",
            "To: {$endDate}",
        ];

        if ($note) {
            $messageLines[] = "Note: {$note}";
        }

        $messageLines[] = "";
        $messageLines[] = "Please review and take necessary action.";

        $message = implode("\n", $messageLines);

        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;
        $failedUsers  = [];

        foreach ($staffProfiles as $index => $staffProfile) {
            try {
                if (!$staffProfile->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff profile has no mobile number for leave request notification', [
                        'staff_id'   => $staffProfile->id,
                        'staff_name' => $staffProfile->name,
                        'request_id' => $request->id,
                    ]);
                    continue;
                }

                if ($index > 0) {
                    sleep($this->messageDelay);
                }

                $result = $this->sendMessageWithRetry($staffProfile->mobile_no, $message);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                    $failedUsers[] = [
                        'staff_name' => $staffProfile->name,
                        'reason'     => $result['message'],
                        'permanent'  => $result['permanent'] ?? false,
                    ];
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Leave request notification error', [
                    'staff_id'   => $staffProfile->id,
                    'request_id' => $request->id,
                    'error'      => $e->getMessage(),
                ]);
                $failedUsers[] = [
                    'staff_name' => $staffProfile->name,
                    'reason'     => $e->getMessage(),
                    'permanent'  => false,
                ];
            }
        }

        Log::info('Leave request notification batch completed', [
            'form_type'   => $formType,
            'request_id'  => $request->id,
            'sent'        => $successCount,
            'failed'      => $failureCount,
            'not_sent'    => $notSentCount,
            'total_staff' => $staffProfiles->count(),
        ]);

        return [
            'success'      => $failureCount === 0,
            'sent'         => $successCount,
            'failed'       => $failureCount,
            'not_sent'     => $notSentCount,
            'total'        => $staffProfiles->count(),
            'failed_users' => $failedUsers,
        ];
    }

    /**
     * Send WhatsApp notification when a new PO request is created (Step 1).
     * Recipients are configured via PO_REQUEST_NOTIFICATION_STAFF_IDS (comma-separated staff IDs).
     */
    public function sendPORequestCreatedNotification($purchaseOrder)
    {
        // Get staff IDs from .env variable (comma-separated, e.g., "1,2,3")
        $staffIds = env('PO_REQUEST_NOTIFICATION_STAFF_IDS', '');

        if (empty($staffIds)) {
            Log::warning('No staff IDs configured in PO_REQUEST_NOTIFICATION_STAFF_IDS for PO request created notification', [
                'po_id' => $purchaseOrder->id,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff IDs configured in .env',
            ];
        }

        $staffIdArray = array_filter(array_map('trim', explode(',', $staffIds)));

        if (empty($staffIdArray)) {
            Log::warning('Invalid PO_REQUEST_NOTIFICATION_STAFF_IDS format in .env', [
                'po_id' => $purchaseOrder->id,
                'value' => $staffIds
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'Invalid staff IDs format',
            ];
        }

        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIdArray)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found with provided IDs for PO request created notification', [
                'po_id' => $purchaseOrder->id,
                'staff_ids' => $staffIdArray,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff profiles found',
            ];
        }

        $poNumber = $purchaseOrder->request_number ?? 'N/A';
        $poType = ucfirst($purchaseOrder->request_type) ?? 'N/A';
        $poDate = $purchaseOrder->date ? $purchaseOrder->date->format('Y-m-d') : 'N/A';

        // Get vendor name
        $vendorName = $purchaseOrder->vendor ? $purchaseOrder->vendor->name : 'N/A';

        // Get company name (only for project and maintenance types)
        $companyName = 'N/A';
        if (in_array($purchaseOrder->request_type, ['project', 'maintenance'])) {
            $companyName = $purchaseOrder->getCompanyName();
        }

        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;

        foreach ($staffProfiles as $index => $staff) {
            try {
                if (!$staff->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff mobile number not found for PO request created notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'po_id' => $purchaseOrder->id,
                        'timestamp' => now()
                    ]);
                    continue;
                }

                $mobile = trim($staff->mobile_no);

                // Validate mobile number format (at least 7 digits)
                if (!preg_match('/\d{7,}/', $mobile)) {
                    $notSentCount++;
                    Log::warning('Staff has invalid mobile number format for PO request created notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'mobile_no' => $mobile,
                        'po_id' => $purchaseOrder->id,
                        'timestamp' => now()
                    ]);
                    continue;
                }

                // Add delay between messages (5 seconds minimum based on API rate limit)
                if ($index > 0) {
                    sleep(5);
                }

                $message = <<<MSG
                📝 *New Purchase Order Request Created*

                PO Number: {$poNumber}
                Type: {$poType}
                Vendor: {$vendorName}
                MSG;

                // Add company name only for project and maintenance types
                if (in_array($purchaseOrder->request_type, ['project', 'maintenance'])) {
                    $message .= "\nCompany: {$companyName}";
                }

                $message .= <<<MSG

                Date: {$poDate}

                A new Purchase Order request has been created and is pending department review.

                Please review and take necessary action.

                Thank you!
                MSG;

                $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post('https://www.wasenderapi.com/api/send-message', [
                        'to' => $mobile,
                        'text' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning('Failed to send PO request created notification', [
                        'mobile' => $mobile,
                        'staff_id' => $staff->id,
                        'po_id' => $purchaseOrder->id,
                        'status_code' => $response->status(),
                        'response' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Exception sending PO request created notification', [
                    'staff_id' => $staff->id,
                    'po_id' => $purchaseOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $failureCount === 0,
            'sent' => $successCount,
            'failed' => $failureCount,
            'not_sent' => $notSentCount,
            'total_staff' => count($staffProfiles),
        ];
    }

    public function sendPOForwardedNotification($purchaseOrder, $urgencyLevel = 'normal')
    {
        // Get staff IDs from .env variable (comma-separated, e.g., "1,2,3")
        $staffIds = env('PO_NOTIFICATION_STAFF_IDS', '');

        if (empty($staffIds)) {
            Log::warning('No staff IDs configured in PO_NOTIFICATION_STAFF_IDS for PO forwarded notification', [
                'po_id' => $purchaseOrder->id,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff IDs configured in .env',
            ];
        }

        // Parse comma-separated IDs
        $staffIdArray = array_filter(array_map('trim', explode(',', $staffIds)));

        if (empty($staffIdArray)) {
            Log::warning('Invalid PO_NOTIFICATION_STAFF_IDS format in .env', [
                'po_id' => $purchaseOrder->id,
                'value' => $staffIds
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'Invalid staff IDs format',
            ];
        }

        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIdArray)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found with provided IDs for PO forwarded notification', [
                'po_id' => $purchaseOrder->id,
                'staff_ids' => $staffIdArray,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff profiles found',
            ];
        }

        $poNumber = $purchaseOrder->request_number ?? 'N/A';
        $poType = ucfirst($purchaseOrder->request_type) ?? 'N/A';
        $poDate = $purchaseOrder->date ? $purchaseOrder->date->format('Y-m-d') : 'N/A';

        // Calculate total from lpout_items (cost breakdown), falling back to items
        $calculatedTotal = 0;
        $lpoutItems = $purchaseOrder->lpout_items ?? [];
        if (!empty($lpoutItems)) {
            foreach ($lpoutItems as $item) {
                $calculatedTotal += floatval($item['total'] ?? 0);
            }
        } elseif (!empty($purchaseOrder->items) && is_array($purchaseOrder->items)) {
            foreach ($purchaseOrder->items as $item) {
                $calculatedTotal += floatval($item['quantity'] ?? 0) * floatval($item['cost'] ?? 0);
            }
        }
        $poAmount = $calculatedTotal > 0 ? 'AED ' . number_format($calculatedTotal, 2) : 'N/A';
        
        // Get vendor name
        $vendorName = $purchaseOrder->vendor ? $purchaseOrder->vendor->name : 'N/A';

        // Get company name (only for project and maintenance types)
        $companyName = 'N/A';
        if (in_array($purchaseOrder->request_type, ['project', 'maintenance'])) {
            $companyName = $purchaseOrder->getCompanyName();
        }

        // Set urgency indicator
        $urgencyIndicator = strtolower($urgencyLevel) === 'urgent' ? '🚨 *URGENT*' : '📌';
        $priorityText = strtolower($urgencyLevel) === 'urgent' ? 'URGENT' : 'Normal';

        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;

        foreach ($staffProfiles as $index => $staff) {
            try {
                if (!$staff->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff mobile number not found for PO notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'po_id' => $purchaseOrder->id,
                        'timestamp' => now()
                    ]);
                    continue;
                }

                $mobile = trim($staff->mobile_no);

                // Validate mobile number format (at least 7 digits)
                if (!preg_match('/\d{7,}/', $mobile)) {
                    $notSentCount++;
                    Log::warning('Staff has invalid mobile number format for PO notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'mobile_no' => $mobile,
                        'po_id' => $purchaseOrder->id,
                        'timestamp' => now()
                    ]);
                    continue;
                }

                // Add delay between messages (5 seconds minimum based on API rate limit)
                if ($index > 0) {
                    sleep(5);
                }

                $message = <<<MSG
                {$urgencyIndicator} *Purchase Order Forwarded to Admin*

                Priority: {$priorityText}
                PO Number: {$poNumber}
                Type: {$poType}
                Vendor: {$vendorName}
                MSG;

                // Add company name only for project and maintenance types
                if (in_array($purchaseOrder->request_type, ['project', 'maintenance'])) {
                    $message .= "\nCompany: {$companyName}";
                }

                $message .= <<<MSG

                Amount: {$poAmount}
                Date: {$poDate}

                A new Purchase Order has been forwarded to you for approval.

                Please review and take necessary action.

                Thank you!
                MSG;

                $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post('https://www.wasenderapi.com/api/send-message', [
                        'to' => $mobile,
                        'text' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning('Failed to send PO forwarded notification', [
                        'mobile' => $mobile,
                        'staff_id' => $staff->id,
                        'po_id' => $purchaseOrder->id,
                        'status_code' => $response->status(),
                        'response' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Exception sending PO forwarded notification', [
                    'staff_id' => $staff->id,
                    'po_id' => $purchaseOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $failureCount === 0,
            'sent' => $successCount,
            'failed' => $failureCount,
            'not_sent' => $notSentCount,
            'total_staff' => count($staffProfiles),
        ];
    }

    public function sendPOAdminApprovalNotification($purchaseOrder)
    {
        $staffIds = env('PO_ADMIN_NOTIFICATION_STAFF_IDS', '');

        if (empty($staffIds)) {
            Log::warning('No staff IDs configured in PO_ADMIN_NOTIFICATION_STAFF_IDS for PO admin approval notification', [
                'po_id' => $purchaseOrder->id,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff IDs configured in .env',
            ];
        }

        $staffIdArray = array_filter(array_map('trim', explode(',', $staffIds)));

        if (empty($staffIdArray)) {
            Log::warning('Invalid PO_ADMIN_NOTIFICATION_STAFF_IDS format in .env', [
                'po_id' => $purchaseOrder->id,
                'value' => $staffIds
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'Invalid staff IDs format',
            ];
        }

        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIdArray)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found for PO admin approval notification', [
                'po_id' => $purchaseOrder->id,
                'staff_ids' => $staffIdArray,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff profiles found',
            ];
        }

        $poNumber = $purchaseOrder->request_number ?? 'N/A';
        $poType = ucfirst($purchaseOrder->request_type) ?? 'N/A';
        $poAmount = $purchaseOrder->total_amount ? 'AED ' . number_format($purchaseOrder->total_amount, 2) : 'N/A';
        $vendorName = $purchaseOrder->vendor ? $purchaseOrder->vendor->name : 'N/A';

        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;

        foreach ($staffProfiles as $index => $staff) {
            try {
                if (!$staff->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff mobile number not found for PO admin approval notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'po_id' => $purchaseOrder->id,
                    ]);
                    continue;
                }

                $mobile = trim($staff->mobile_no);

                if (!preg_match('/\d{7,}/', $mobile)) {
                    $notSentCount++;
                    Log::warning('Staff has invalid mobile number format for PO admin approval notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'mobile_no' => $mobile,
                        'po_id' => $purchaseOrder->id,
                    ]);
                    continue;
                }

                if ($index > 0) {
                    sleep(5);
                }

                $message = <<<MSG
                ✅ *Purchase Order Approved by Admin*

                PO Number: {$poNumber}
                Type: {$poType}
                Vendor: {$vendorName}
                Amount: {$poAmount}

                The PO has been approved and LPOUT has been generated.

                Status: APPROVED

                Thank you!
                MSG;

                $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post('https://www.wasenderapi.com/api/send-message', [
                        'to' => $mobile,
                        'text' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning('Failed to send PO admin approval notification', [
                        'mobile' => $mobile,
                        'staff_id' => $staff->id,
                        'po_id' => $purchaseOrder->id,
                        'status_code' => $response->status(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Exception sending PO admin approval notification', [
                    'staff_id' => $staff->id,
                    'po_id' => $purchaseOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $failureCount === 0,
            'sent' => $successCount,
            'failed' => $failureCount,
            'not_sent' => $notSentCount,
            'total_staff' => count($staffProfiles),
        ];
    }

    public function sendPOAdminRejectionNotification($purchaseOrder, $rejectionReason = '')
    {
        $staffIds = env('PO_ADMIN_NOTIFICATION_STAFF_IDS', '');

        if (empty($staffIds)) {
            Log::warning('No staff IDs configured in PO_ADMIN_NOTIFICATION_STAFF_IDS for PO admin rejection notification', [
                'po_id' => $purchaseOrder->id,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff IDs configured in .env',
            ];
        }

        $staffIdArray = array_filter(array_map('trim', explode(',', $staffIds)));

        if (empty($staffIdArray)) {
            Log::warning('Invalid PO_ADMIN_NOTIFICATION_STAFF_IDS format in .env', [
                'po_id' => $purchaseOrder->id,
                'value' => $staffIds
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'Invalid staff IDs format',
            ];
        }

        $staffProfiles = \App\Models\StafProfile::whereIn('id', $staffIdArray)->get();

        if ($staffProfiles->isEmpty()) {
            Log::warning('No staff profiles found for PO admin rejection notification', [
                'po_id' => $purchaseOrder->id,
                'staff_ids' => $staffIdArray,
                'timestamp' => now()
            ]);
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'not_sent' => 0,
                'message' => 'No staff profiles found',
            ];
        }

        $poNumber = $purchaseOrder->request_number ?? 'N/A';
        $poType = ucfirst($purchaseOrder->request_type) ?? 'N/A';
        $poAmount = $purchaseOrder->total_amount ? 'AED ' . number_format($purchaseOrder->total_amount, 2) : 'N/A';
        $vendorName = $purchaseOrder->vendor ? $purchaseOrder->vendor->name : 'N/A';
        $reasonText = $rejectionReason ? "\nReason: {$rejectionReason}" : '';

        $successCount = 0;
        $failureCount = 0;
        $notSentCount = 0;

        foreach ($staffProfiles as $index => $staff) {
            try {
                if (!$staff->mobile_no) {
                    $notSentCount++;
                    Log::warning('Staff mobile number not found for PO admin rejection notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'po_id' => $purchaseOrder->id,
                    ]);
                    continue;
                }

                $mobile = trim($staff->mobile_no);

                if (!preg_match('/\d{7,}/', $mobile)) {
                    $notSentCount++;
                    Log::warning('Staff has invalid mobile number format for PO admin rejection notification', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'mobile_no' => $mobile,
                        'po_id' => $purchaseOrder->id,
                    ]);
                    continue;
                }

                if ($index > 0) {
                    sleep(5);
                }

                $message = <<<MSG
                ❌ *Purchase Order Rejected by Admin*

                PO Number: {$poNumber}
                Type: {$poType}
                Vendor: {$vendorName}
                Amount: {$poAmount}{$reasonText}

                The PO has been rejected and requires revision.

                Status: REJECTED

                Please contact the admin for further details.

                Thank you!
                MSG;

                $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post('https://www.wasenderapi.com/api/send-message', [
                        'to' => $mobile,
                        'text' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning('Failed to send PO admin rejection notification', [
                        'mobile' => $mobile,
                        'staff_id' => $staff->id,
                        'po_id' => $purchaseOrder->id,
                        'status_code' => $response->status(),
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::error('Exception sending PO admin rejection notification', [
                    'staff_id' => $staff->id,
                    'po_id' => $purchaseOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $failureCount === 0,
            'sent' => $successCount,
            'failed' => $failureCount,
            'not_sent' => $notSentCount,
            'total_staff' => count($staffProfiles),
        ];
    }
}