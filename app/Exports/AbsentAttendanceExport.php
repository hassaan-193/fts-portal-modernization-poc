<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsentAttendanceExport implements FromView, ShouldAutoSize
{
    private $data;
    private $filters;

    public function __construct($data, $filters = [])
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function view(): View
    {
        // Pre-calculate approved absence count per person
        $approvedByPerson = [];
        foreach ($this->data as $row) {
            $laborId = (int)$row->labor_id; // Cast to integer to ensure consistency
            if (!isset($approvedByPerson[$laborId])) {
                $approvedByPerson[$laborId] = $this->data->where('labor_id', $laborId)->count();
            }
        }

        // Debug logging
        \Log::info('AbsentAttendanceExport - Data Count: ' . count($this->data));
        \Log::info('AbsentAttendanceExport - ApprovedByPerson: ' . json_encode($approvedByPerson));
        \Log::info('AbsentAttendanceExport - First Row: ' . json_encode($this->data->first()));

        return view('exports.absent_attendance_report', [
            'data' => $this->data,
            'filters' => $this->filters,
            'approvedByPerson' => $approvedByPerson
        ]);
    }
}
