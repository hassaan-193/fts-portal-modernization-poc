<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserAttendanceExport implements FromView, ShouldAutoSize
{
    private $user;
    private $sessions;
    private $startDate;
    private $endDate;

    public function __construct($user, $sessions, $startDate, $endDate)
    {
        $this->user = $user;
        $this->sessions = $sessions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $groupedByDate = $this->sessions->groupBy(function ($session) {
            return $session->session_date->format('Y-m-d');
        });

        $lateDaysShift1 = $this->sessions->where('shift_window', 'shift_1')->where('is_late', true)
            ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
        $lateDaysShift2 = $this->sessions->where('shift_window', 'shift_2')->where('is_late', true)
            ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
        $lateDays = $lateDaysShift1 + $lateDaysShift2;

        $absentDays = 0;
        $today = Carbon::today();
        $rangeEnd = $this->endDate->copy()->lt($today) ? $this->endDate->copy() : $today->copy();
        $checkDate = $this->startDate->copy();
        while ($checkDate->lte($rangeEnd)) {
            if (!$checkDate->isSunday()) {
                if (!$groupedByDate->has($checkDate->format('Y-m-d'))) {
                    $absentDays++;
                }
            }
            $checkDate->addDay();
        }

        return view('exports.user_attendance_report', [
            'user' => $this->user,
            'sessions' => $this->sessions,
            'groupedByDate' => $groupedByDate,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'rangeEnd' => $rangeEnd,
            'lateDaysShift1' => $lateDaysShift1,
            'lateDaysShift2' => $lateDaysShift2,
            'lateDays' => $lateDays,
            'absentDays' => $absentDays,
        ]);
    }
}
