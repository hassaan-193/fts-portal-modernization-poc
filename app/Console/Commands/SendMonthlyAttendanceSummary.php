<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceSession;
use App\Services\AttendanceReportService;
use App\Mail\MonthlyAttendanceSummaryMail;
use App\Exports\AllUsersAttendanceExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SendMonthlyAttendanceSummary extends Command
{
    protected $signature = 'attendance:send-monthly-summary
                            {--month= : Target month in YYYY-MM format (defaults to previous month)}';

    protected $description = 'Generate and email the monthly attendance summary (late arrivals & absences)';

    public function handle(AttendanceReportService $reportService)
    {
        $recipients = config('attendance.monthly_summary_recipients', []);

        if (empty($recipients)) {
            $this->warn('No recipients configured. Set ATTENDANCE_MONTHLY_RECIPIENTS in .env');
            return 1;
        }

        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth()
            : null;

        $this->info('Generating monthly attendance summary...');
        $summaryData = $reportService->generateMonthlySummary($month);

        $this->info("Period: {$summaryData['month']} {$summaryData['year']}");
        $this->info("Users evaluated: {$summaryData['users']->count()}");

        $this->table(
            ['Employee', 'Morning Late', 'Evening Late', 'Absent'],
            $summaryData['users']->map(fn ($u) => [
                $u['user_name'],
                $u['morning_late'],
                $u['evening_late'],
                $u['total_absent'],
            ])->toArray()
        );

        // Generate the Excel attachment (same as the all-users report export)
        $startDate = Carbon::parse($summaryData['start']);
        $endDate = Carbon::parse($summaryData['end']);
        $excludedIds = config('attendance.excluded_user_ids', []);

        $allSessions = AttendanceSession::whereBetween('session_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->whereNotIn('user_id', $excludedIds)
            ->with('user')
            ->orderBy('session_date', 'asc')
            ->orderBy('clock_in_time', 'asc')
            ->get();

        $excelFilename = "attendance_{$summaryData['month']}_{$summaryData['year']}.xlsx";
        $excelPath = storage_path("app/temp/{$excelFilename}");

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        Excel::store(
            new AllUsersAttendanceExport($allSessions, $startDate, $endDate),
            "temp/{$excelFilename}",
            'local'
        );

        $this->info("Excel report generated: {$excelFilename}");

        $mailable = new MonthlyAttendanceSummaryMail($summaryData, $excelPath);

        try {
            Mail::to($recipients)->send($mailable);
            $this->info('Email sent to: ' . implode(', ', $recipients));
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        } finally {
            if (file_exists($excelPath)) {
                unlink($excelPath);
            }
        }

        return 0;
    }
}
