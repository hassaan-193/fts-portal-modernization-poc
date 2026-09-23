<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresentsOvertimeExport implements FromView, ShouldAutoSize
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
        return view('exports.presents_overtime_report', [
            'data' => $this->data,
            'filters' => $this->filters
        ]);
    }
}
