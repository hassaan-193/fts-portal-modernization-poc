<?php

namespace App\Console\Commands;

use App\Models\StafProfile;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBirthdayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:send-notifications';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send birthday wishes to staff and notify managers about upcoming birthdays';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting birthday notification process...');

        try {
            // Get staff with birthdays today
            $staffsWithBirthday = $this->getStaffsWithBirthdayToday();

            if (empty($staffsWithBirthday)) {
                $this->info('No staff with birthdays today.');
                Log::info('Birthday notification command: No staff with birthdays today');
                return 0;
            }

            $this->info(count($staffsWithBirthday) . ' staff member(s) have birthdays today.');

            // Initialize WhatsApp service
            $whatsAppService = app(WhatsAppService::class);

            // Send birthday wishes to each staff member
            $wishesCount = 0;
            $wishesFailedCount = 0;
            
            foreach ($staffsWithBirthday as $staff) {
                try {
                    $this->info("Sending birthday wishes to {$staff->name}...");
                    if ($whatsAppService->sendBirthdayWishes($staff)) {
                        $wishesCount++;
                        $this->info("✓ Wishes sent to {$staff->name}");
                    } else {
                        $wishesFailedCount++;
                        $this->warn("✗ Failed to send wishes to {$staff->name}");
                    }
                } catch (\Exception $e) {
                    $wishesFailedCount++;
                    $this->error("✗ Error sending wishes to {$staff->name}: " . $e->getMessage());
                    Log::error('Error sending birthday wishes', [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'error' => $e->getMessage()
                    ]);
                    // Continue to next staff member
                    continue;
                }
                
                // Add delay between messages (to respect API rate limits)
                sleep(5);
            }

            // Get manager phone numbers from .env
            $managerPhones = env('MANAGGER_BIRTHDAY_NOTIFICATION_FOR_STAFF', '');

            if (!empty($managerPhones)) {
                $this->info("Sending birthday notifications to managers...");
                $result = $whatsAppService->sendBirthdayNotificationsToManagers(
                    $staffsWithBirthday,
                    $managerPhones
                );

                if ($result['success']) {
                    $this->info("✓ Birthday notifications sent successfully!");
                    $this->info("  - Staff wishes sent: {$wishesCount}/{count($staffsWithBirthday)}");
                    $this->info("  - Managers notified: {$result['sent']}/{$result['managers_notified']}");
                } else {
                    $this->warn("Some notifications had issues: {$result['message']}");
                    $this->info("  - Staff wishes sent: {$wishesCount}/{count($staffsWithBirthday)}");
                    $this->info("  - Managers notified: {$result['sent']}/{$result['managers_notified']}");
                }
            } else {
                $this->warn("No manager phone numbers configured in .env (MANAGGER_BIRTHDAY_NOTIFICATION_FOR_STAFF)");
                $this->info("  - Staff wishes sent: {$wishesCount}/{count($staffsWithBirthday)}");
            }

            Log::info('Birthday notification command completed', [
                'staff_count' => count($staffsWithBirthday),
                'wishes_sent' => $wishesCount,
                'wishes_failed' => $wishesFailedCount
            ]);

            $this->info('Birthday notification process completed!');
            return 0;

        } catch (\Exception $e) {
            Log::error('Birthday notification command error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Get all staff with birthdays today
     *
     * @return array
     */
    private function getStaffsWithBirthdayToday()
    {
        $today = now();
        $month = $today->month;
        $day = $today->day;

        // Query staff with birthdays today
        return StafProfile::whereRaw("MONTH(dob) = {$month} AND DAY(dob) = {$day}")
            ->get();
    }
}
