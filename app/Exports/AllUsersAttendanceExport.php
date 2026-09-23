<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllUsersAttendanceExport implements FromView, ShouldAutoSize
{
    private $allSessions;
    private $startDate;
    private $endDate;

    public function __construct($allSessions, $startDate, $endDate)
    {
        $this->allSessions = $allSessions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $sessionsByUser = $this->allSessions->groupBy('user_id');
        $today = Carbon::today();
        $rangeEnd = $this->endDate->copy()->lt($today) ? $this->endDate->copy() : $today->copy();

        $userBlocks = $sessionsByUser->map(function ($userSessions) use ($rangeEnd) {
            $user = $userSessions->first()->user;

            $sessionsByDate = $userSessions->groupBy(fn ($s) => $s->session_date->format('Y-m-d'));
            $daysWorked = $sessionsByDate->count();

            $lateDaysShift1 = $userSessions->where('shift_window', 'shift_1')->where('is_late', true)
                ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
            $lateDaysShift2 = $userSessions->where('shift_window', 'shift_2')->where('is_late', true)
                ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
            $lateDays = $lateDaysShift1 + $lateDaysShift2;

            $absentDays = 0;
            $checkDate = $this->startDate->copy();
            while ($checkDate->lte($rangeEnd)) {
                if (!$checkDate->isSunday()) {
                    if (!$sessionsByDate->has($checkDate->format('Y-m-d'))) {
                        $absentDays++;
                    }
                }
                $checkDate->addDay();
            }

            return [
                'user' => $user,
                'sessionsByDate' => $sessionsByDate,
                'daysWorked' => $daysWorked,
                'lateDaysShift1' => $lateDaysShift1,
                'lateDaysShift2' => $lateDaysShift2,
                'lateDays' => $lateDays,
                'absentDays' => $absentDays,
            ];
        });

        return view('exports.all_users_attendance_report', [
            'userBlocks' => $userBlocks,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
