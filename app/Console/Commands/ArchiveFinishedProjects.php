<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\VisitScheduleHistory;
use Carbon\Carbon;

class ArchiveFinishedProjects extends Command
{
    protected $signature = 'projects:archive-finished';
    protected $description = 'Move fully-visited, year-old projects into history';

    public function handle()
    {
        $today = Carbon::today();
    
        Project::with(['quotation.company', 'history'])
            ->get()
            ->filter(function (Project $project) use ($today) {
                $endDate = Carbon::parse($project->date)->addYear();
    
                return is_null($project->history)
                    && $today->gte($endDate)
                    && $project->category === 'amc';
            })
            ->each(function (Project $project) {
                $endDate = Carbon::parse($project->date)->addYear();
    
                VisitScheduleHistory::create([
                    'project_id'        => $project->id,
                    'visit_schedule_id' => null,
                    'visit_date'        => $endDate,  // storing end date here
                    'status'            => 'done',
                    'company_name'      => $project->quotation->company->name ?? 'Unknown Company',
                    'project_name'      => $project->subject ?? 'Unnamed Project',
                ]);
            });
    
        $this->info('✅ AMC Projects archive check complete.');
    }
    
    
    
}