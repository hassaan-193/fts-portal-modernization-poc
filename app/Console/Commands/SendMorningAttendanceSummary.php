<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceSession;
use App\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class SendMorningAttendanceSummary extends Command
{
    protected $signature = 'attendance:send-morning-summary';
    protected $description = 'Send morning attendance summary at 8:30 AM';

    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        parent::__construct();
        $this->whatsAppService = $whatsAppService;
    }

    public function handle()
    {
        try {
            $today = Carbon::today();
            $isSaturday = $today->isSaturday();

            // Sunday is the weekly day off – skip
            if ($today->isSunday()) {
                $this->info('Skipping attendance summary — Sunday (day off).');
                return 0;
            }

            $excludedUserIds = config('attendance.excluded_user_ids', [1, 7, 13, 18, 19, 20]);
            $allUsers = User::whereNotIn('id', $excludedUserIds)->get();

            $presentUsers = collect();
            $lateUsers = collect();
            $absentUsers = collect();

            // Shift 1: on-time through 08:00:59 weekdays (late from 8:01 AM), Mon–Fri incl. Friday; Sat through 09:00:00
            $onTimeStart = $isSaturday ? '09:00:00' : '07:00:00';
            $onTimeEnd   = $isSaturday ? '09:00:00' : '08:00:59';
            $lateEnd     = $isSaturday ? '09:30:00' : '08:30:00';

            foreach ($allUsers as $user) {
                $onTimeSession = AttendanceSession::where('user_id', $user->id)
                    ->whereDate('session_date', $today)
                    ->whereTime('clock_in_time', '>=', $onTimeStart)
                    ->whereTime('clock_in_time', '<=', $onTimeEnd)
                    ->first();

                if ($onTimeSession) {
                    $presentUsers->push([
                        'user' => $user,
                        'clock_in_time' => $onTimeSession->clock_in_time,
                    ]);
                } else {
                    $lateSession = AttendanceSession::where('user_id', $user->id)
                        ->whereDate('session_date', $today)
                        ->whereTime('clock_in_time', '>', $onTimeEnd)
                        ->whereTime('clock_in_time', '<=', $lateEnd)
                        ->first();

                    if ($lateSession) {
                        $lateUsers->push([
                            'user' => $user,
                            'clock_in_time' => $lateSession->clock_in_time,
                        ]);
                    } else {
                        $absentUsers->push($user);
                    }
                }
            }

            // Send WhatsApp notification
            $whatsappResult = $this->whatsAppService->sendAttendanceSummary(
                $presentUsers,
                $lateUsers,
                $absentUsers
            );

            // Send Email notification
            $emailResult = $this->whatsAppService->sendAttendanceSummaryEmail(
                $presentUsers,
                $lateUsers,
                $absentUsers
            );

            $this->info('✅ Morning attendance summary sent successfully.');
            $this->info('');
            $this->info('📊 Summary:');
            $this->info('Present: ' . $presentUsers->count());
            $this->info('Late: ' . $lateUsers->count());
            $this->info('Absent: ' . $absentUsers->count());
            $this->info('');
            $this->info('📱 WhatsApp:');
            $this->info('Messages Sent: ' . $whatsappResult['sent'] . '/' . $whatsappResult['total']);
            $this->info('');
            $this->info('📧 Email:');
            $this->info('Status: ' . ($emailResult['success'] ? '✅ Success' : '❌ Failed'));
            $this->info('Recipients: ' . $emailResult['sent']);

        } catch (\Exception $e) {
            $this->error('❌ Error sending attendance summary: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
