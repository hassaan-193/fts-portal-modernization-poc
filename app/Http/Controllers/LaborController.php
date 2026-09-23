<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\StafProfile;
use App\Models\Project;
use App\Models\VisitSchedule;
use App\Models\LabourAssignment;
use App\DataTables\LabourAssignmentsDataTable;

class LaborController extends Controller
{
        public function showLaborSystem()
    {
        return view('labor_system.labor_system');
    }

    public function getLaborers(Request $request)
    {
        $specifiedDate = $request->input('date', now()->toDateString());
    
        $laborers = StafProfile::where('staf_type', 'labor')
            ->whereNotIn('id', function ($query) use ($specifiedDate) {
                $query->select('labor_id')
                      ->from('labour_assignments')
                      ->whereDate('assignment_start_date', '<=', $specifiedDate)
                      ->where(function ($q) use ($specifiedDate) {
                          $q->whereDate('assignment_end_date', '>=', $specifiedDate)
                            ->orWhereNull('assignment_end_date');
                      });
            })
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
            'assignments.*.labor_ids.*.id' => 'required|exists:staf_profile,id',
            'assignments.*.labor_ids.*.quantity' => 'nullable|integer|min:0',
            'assignments.*.start_date' => 'required|date',
            'assignments.*.end_date' => 'required|date|after_or_equal:assignments.*.start_date',
            'assignments.*.hours_worked' => 'required|integer',
        ]);
    
        foreach ($validated['assignments'] as $assignment) {
            foreach ($assignment['labor_ids'] as $laborData) {
                $laborId = $laborData['id'];
                $overtime = $laborData['quantity'] ?? 0;
                $baseHours = $assignment['hours_worked'];
                $finalHours = $baseHours + $overtime;
    
                $conflict = LabourAssignment::where('labor_id', $laborId)
                    ->where(function ($query) use ($assignment) {
                        $query->whereBetween('assignment_start_date', [$assignment['start_date'], $assignment['end_date']])
                            ->orWhereBetween('assignment_end_date', [$assignment['start_date'], $assignment['end_date']])
                            ->orWhere(function ($q) use ($assignment) {
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
                    'hours_worked' => $finalHours,        // Default + overtime
                    'overtime_hours' => $overtime,         // Store overtime separately
                ]);
            }
        }
    
        return response()->json(['message' => 'Bulk assignments completed successfully']);
    }
    public function getLabourAssignments(LabourAssignmentsDataTable $dataTable)
    {
        return $dataTable->render('labor_system.view');
    }
    public function monthlyReport(Request $request)
    {
        $labors = StafProfile::all();
        if ($request->filled('labor_id') && $request->filled('month')) {
            $start = Carbon::parse($request->month)->startOfMonth();
            $end = Carbon::parse($request->month)->endOfMonth();
            $reportData = LabourAssignment::with('project')
                ->where('labor_id', $request->labor_id)
                ->whereBetween('assignment_start_date', [$start, $end])
                ->get();
            $totalOvertimeHours = $reportData->sum('overtime_hours'); // Sum of overtime hours
            $selectedLabor = StafProfile::find($request->labor_id);
            return view('labor_system.monthly_report', compact('labors', 'reportData', 'selectedLabor', 'totalOvertimeHours'));
        }
        return view('labor_system.monthly_report', compact('labors'));
    }

    public function deleteGroup(Request $request)
    {
        $projectId = $request->input('project_id');
        $assignmentStartDate = $request->input('assignment_start_date');
        // Delete records that match the project_id and assignment_start_date
        $deletedCount = LabourAssignment::where('project_id', $projectId)
                                        ->where('assignment_start_date', $assignmentStartDate)
                                        ->delete();
    
        if ($deletedCount) {
            return redirect()->back()->with('success', 'Attendance group deleted successfully.');
        } 
        else {
            return redirect()->back()->with('error', 'No records found to delete.');
        }
    }
    
    public function editGroup(Request $request)
    {
        $projectId = $request->input('project_id');
        $assignmentStartDate = $request->input('assignment_start_date');

        $assignments = LabourAssignment::with('labor')
                        ->where('project_id', $projectId)
                        ->where('assignment_start_date', $assignmentStartDate)
                        ->get();

        if ($assignments->isEmpty()) {
            return redirect()->back()->with('error', 'No assignments found to edit.');
        }

        return view('labor_system.edit_group', compact('assignments', 'projectId', 'assignmentStartDate'));

    }

    public function updateGroup(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assignment_start_date' => 'required|date',
            'assignments' => 'required|array',
            'assignments.*.id' => 'required|exists:labour_assignments,id',
            'assignments.*.hours_worked' => 'required|numeric|min:0',
            'assignments.*.overtime_hours' => 'required|numeric|min:0',
        ]);

        foreach ($validated['assignments'] as $data) {
            $assignment = LabourAssignment::findOrFail($data['id']);
            $base_hours = $assignment->hours_worked - $assignment->overtime_hours;
            $new_total_hours = $base_hours + $data['overtime_hours'];
            $assignment->update([
                'hours_worked' => $new_total_hours,
                'overtime_hours' => $data['overtime_hours'],
            ]);
        }
        

        return redirect()->route('labor.assignments')->with('success', 'Attendance group updated successfully.');

    }
    
}