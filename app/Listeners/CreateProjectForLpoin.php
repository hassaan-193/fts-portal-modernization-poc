<?php

namespace App\Listeners;

use App\Events\LpoinCreated;
use App\Repositories\ProjectRepository;
use App\Models\VisitSchedule;
use Carbon\Carbon;

class CreateProjectForLpoin
{
    protected $projectRepo;

    public function __construct(ProjectRepository $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    public function handle(LpoinCreated $event)
    {
        $lpoin = $event->lpoin;  
        $lpoin->load('quotation');
        $quotation = $lpoin->quotation;
        

        if (!$quotation || $quotation->category !== 'amc') {
            return;
        }

        // check if project exists
        if ($quotation->project) {
            return;
        }

        $visits = $quotation->number_of_visits ?? 4;
        $startDate = Carbon::parse($event->startDate);
        $visitSchedule = [];

        $visitSchedule[] = $startDate->toDateString();

        if ($visits > 1) {
            if ($visits <= 4) {
                for ($i = 1; $i < $visits; $i++) {
                    $visitSchedule[] = $startDate->copy()->addMonths($i * 3)->toDateString();
                }
            } else {
                $intervalDays = 365 / $visits;
                for ($i = 1; $i < $visits; $i++) {
                    $visitSchedule[] = $startDate->copy()->addDays($i * $intervalDays)->toDateString();
                }
            }
        }

        $input = [
            'quotation_id'    => $quotation->id,
            'category'        => $quotation->category,
            'visits'          => $visits,
            'visit_schedule'  => json_encode($visitSchedule),
            'date'            => $startDate->toDateString(),
            'subject'        => $event->subject, 

        ];

        $project = $this->projectRepo->create($input);

        foreach ($visitSchedule as $date) {
            $status = Carbon::parse($date)->isPast() ? 'pending' : (Carbon::parse($date)->isFuture() ? 'upcoming' : 'pending');
            VisitSchedule::create([
                'project_id' => $project->id,
                'visit_date' => $date,
                'status'     => $status,
                'file_uploaded' => false,
            ]);
        }
    }
}
