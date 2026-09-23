<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProjectVisitCard extends Component
{
    public $project;
    public $totalVisits;
    public $visitSchedule;

    public function __construct($project, $totalVisits, $visitSchedule)
    {
        $this->project = $project;
        $this->totalVisits = $totalVisits;
        $this->visitSchedule = $visitSchedule;
    }

    public function render()
    {
        return view('components.project-visit-card');
    }
}
