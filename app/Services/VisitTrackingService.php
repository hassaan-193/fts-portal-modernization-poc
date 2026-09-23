<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;

class VisitTrackingService
{
    public function getVisitTrackingData($userId)
    {
        $projects = Project::where('user_id', $userId)->get();
        $projectsVisitData = [];

        foreach ($projects as $project) {
            // Improved check for missing relationships
            $companyName = $project->quotation ? $project->quotation->company->name : 'N/A';

            // Decode the visit_schedule and handle empty or null values
            $visitSchedule = json_decode($project->visit_schedule, true);
            if (!$visitSchedule || empty($visitSchedule)) {
                continue;
            }

            // Map visit dates to Carbon instances
            $visitDates = collect($visitSchedule)->map(function ($date) {
                try {
                    return Carbon::parse($date);
                } catch (\Exception $e) {
                    // Log any parsing errors
                    \Log::error('Error parsing date: ' . $date, ['exception' => $e->getMessage()]);
                    return null; // Skip invalid dates
                }
            })->filter(); // Filter out any null values due to parsing errors

            // Get current date for comparison
            $currentDate = Carbon::now();

            // Filter upcoming visits
            $upcomingVisit = $visitDates->filter(function ($visit) use ($currentDate) {
                return $visit->isAfter($currentDate);
            })->sortBy(function ($visit) {
                return $visit->timestamp;
            })->first();

            // Calculate the end date (365 days after the start date)
            $startDate = Carbon::parse($project->date);
            $endDate = $startDate->addDays(365);

            // Add project data to the results array
            $projectsVisitData[] = [
                'project' => $project,
                'company_name' => $companyName,
                'upcoming_visit' => $upcomingVisit ? $upcomingVisit->toDateString() : 'N/A',
                'end_date' => $endDate->toDateString(),
            ];
        }

        return $projectsVisitData;
    }

}
