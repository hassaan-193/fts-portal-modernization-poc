<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyAttendanceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $summaryData;
    protected $excelPath;

    public function __construct(array $summaryData, string $excelPath = null)
    {
        $this->summaryData = $summaryData;
        $this->excelPath = $excelPath;
    }

    public function build()
    {
        $mail = $this->subject("Monthly Attendance Summary - {$this->summaryData['month']} {$this->summaryData['year']}")
            ->view('emails.monthly-attendance-summary')
            ->with(['data' => $this->summaryData]);

        if ($this->excelPath && file_exists($this->excelPath)) {
            $mail->attach($this->excelPath, [
                'as' => "Attendance_Report_{$this->summaryData['month']}_{$this->summaryData['year']}.xlsx",
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return $mail;
    }
}
