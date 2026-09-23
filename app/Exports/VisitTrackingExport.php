<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VisitTrackingExport implements FromView
{
    private $projects;
    private $filter;

    public function __construct($projects, $filter = '')
    {
        $this->projects = $projects;
        $this->filter = $filter;
    }

    public function view(): View
    {
        return view('exports.visit_tracking_excel', [
            'projects' => $this->projects,
            'filter' => $this->filter
        ]);
    }
}