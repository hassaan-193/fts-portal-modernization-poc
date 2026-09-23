<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StafProfile;
use App\Models\Project;
use App\Models\VisitSchedule;
use App\Models\LabourAssignment;

class LaborController extends Controller
{
    public function clearLabourAssignments()
    {
        // Optional: Add a confirmation prompt or authorization logic here if needed
        
        LabourAssignment::truncate();

        return redirect()->route('labour-assignments.index')->with('success', 'All labour assignments have been cleared.');
    }

    public function getLaborers()
    {

        // Get today's date
        $today = now()->toDateString();
    
        // Fetch laborers, checking assignment end date logic
        $laborers = StafProfile::where('staf_type', 'labor')
            ->leftJoin('labour_assignments', 'staf_profile.id', '=', 'labour_assignments.labor_id')
            ->where(function($query) use ($today) {
                $query->whereNull('labour_assignments.assignment_end_date')
                    ->orWhereDate('labour_assignments.assignment_end_date', '<', $today);
            })
            ->select('staf_profile.*')
            ->get();
    
        return response()->json($laborers);
    }

    // Fetch projects with optional filtering by category
    public function getProjects(Request $request)
    {
        $category = $request->query('category');
        $query = Project::query();

        if ($category) {
            $query->where('category', $category);
        }

        $projects = $query->get();

        

        return response()->json($projects);
    }

    // Fetch visit schedules for a specific AMC project
    public function getVisitSchedules(Request $request)
    {
        $projectId = $request->query('project_id');

        $visitSchedules = VisitSchedule::where('project_id', $projectId)
            ->whereIn('status', ['pending', 'upcoming'])
            ->get();

        return response()->json($visitSchedules);
    }

    public function assignLabor(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.project_id' => 'required|exists:projects,id',
            'assignments.*.labor_ids' => 'required|array',
            'assignments.*.labor_ids.*' => 'exists:staf_profile,id',
            'assignments.*.start_date' => 'required|date',
            'assignments.*.end_date' => 'required|date|after_or_equal:assignments.*.start_date',
            'assignments.*.hours_worked' => 'required|integer', 

        ]);

        foreach ($validated['assignments'] as $assignment) {
            foreach ($assignment['labor_ids'] as $laborId) {
                // Check for overlapping assignments
                $conflict = LabourAssignment::where('labor_id', $laborId)
                    ->where(function($query) use ($assignment) {
                        $query->whereBetween('assignment_start_date', [$assignment['start_date'], $assignment['end_date']])
                            ->orWhereBetween('assignment_end_date', [$assignment['start_date'], $assignment['end_date']])
                            ->orWhere(function($q) use ($assignment) {
                                $q->where('assignment_start_date', '<=', $assignment['end_date'])
                                    ->where('assignment_end_date', '>=', $assignment['start_date']);
                            });
                    })
                    ->exists();

                if ($conflict) {
                    return response()->json([
                        'message' => "Labor ID $laborId has conflicting assignments"
                    ], 400);
                }

                LabourAssignment::create([
                    'labor_id' => $laborId,
                    'project_id' => $assignment['project_id'],
                    'visit_schedule_id' => $assignment['visit_schedule_id'],
                    'assignment_start_date' => $assignment['start_date'],
                    'assignment_end_date' => $assignment['end_date'],
                    'hours_worked' => $assignment['hours_worked'], // Added hours worked here

                ]);
            }
        }

        return response()->json(['message' => 'Bulk assignments completed successfully']);
    }

    public function getLabourAssignments()
    {
        $assignments = LabourAssignment::with(['labor', 'project', 'visitSchedule']) // Include visitSchedule in the relationships
            ->get()
            ->groupBy(function($assignment) {
                // Group by both project_id and visit_schedule_id
                return $assignment->project_id . '-' . $assignment->visit_schedule_id;
            })
            ->map(function ($group, $key) {
                $firstAssignment = $group->first();
                return [
                    'project_name' => $firstAssignment->project->subject ?? 'N/A',
                    'visit_schedule' => $firstAssignment->visitSchedule ? $firstAssignment->visitSchedule->visit_date : 'N/A', // Visit schedule date
                    'labor_names' => $group->map(fn($assignment) => $assignment->labor->name ?? 'N/A')->join(', '),
                    'hours_worked' => $group->map(fn($assignment) => $assignment->hours_worked ?? 'N/A')->join(', '), // Add hours worked
                    'date' => $firstAssignment->assignment_start_date, // Use a representative date
                ];
            });

        return view('labor_system.view', ['assignments' => $assignments]);
    }

}

