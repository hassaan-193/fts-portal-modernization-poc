<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Models\StafProfile;
use App\Models\StaffRating;
use App\Models\MonthlyStaffReport;
use Illuminate\Support\Facades\Auth;
use App\DataTables\StaffRatingDataTable;
use App\DataTables\StaffRatingReportDataTable;
use App\DataTables\MonthlyStaffReportDataTable;

class StaffRatingController extends Controller
{
    public function index(Request $request)
    {
        $staffList = StafProfile::where('staf_type', 'Labor')->get();

        $userId = Auth::id();

        $monthYear = $request->query('month_year');
        if ($monthYear) {
            [$year, $month] = explode('-', $monthYear);
        } else {
            $month = now()->month;
            $year = now()->year;
        }

        $userRatings = StaffRating::where('engineer_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('staff_id');

        $monthlyReports = MonthlyStaffReport::where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('staff_id');

        return view('staff_ratings.index', compact('staffList', 'userRatings', 'monthlyReports', 'month', 'year'));
    }

    public function store(Request $request, StafProfile $stafProfile)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $validated = $request->validate([
            'safety_compliance' => 'required|integer|min:0|max:10',
            'communication' => 'required|integer|min:0|max:10',
            'attendance' => 'required|integer|min:0|max:10',
            'time_management' => 'required|integer|min:0|max:10',
            'job_responsibility' => 'required|integer|min:0|max:10',
            'material_handling' => 'required|integer|min:0|max:10',
            'document_handling' => 'required|integer|min:0|max:10',
            'competency' => 'required|integer|min:0|max:10',
        ]);

        $alreadyRated = StaffRating::where([
            'staff_id' => $stafProfile->id,
            'engineer_id' => Auth::id(),
            'month' => $month,
            'year' => $year,
        ])->exists();

        if ($alreadyRated) {
            return back()->with('error', 'You have already rated this staff member for this month.');
        }

        StaffRating::create([
            'staff_id' => $stafProfile->id,
            'engineer_id' => Auth::id(),
            'month' => $month,
            'year' => $year,
            'safety_compliance' => $validated['safety_compliance'],
            'communication' => $validated['communication'],
            'attendance' => $validated['attendance'],
            'time_management' => $validated['time_management'],
            'job_responsibility' => $validated['job_responsibility'],
            'material_handling' => $validated['material_handling'],
            'document_handling' => $validated['document_handling'],
            'competency' => $validated['competency'],
        ]);

        $ratings = StaffRating::where([
            'staff_id' => $stafProfile->id,
            'month' => $month,
            'year' => $year,
        ])->get();

        if ($ratings->count() === 6) {
            $averages = [
                'safety_avg' => round($ratings->avg('safety_compliance'), 2),
                'communication_avg' => round($ratings->avg('communication'), 2),
                'attendance_avg' => round($ratings->avg('attendance'), 2),
                'time_management_avg' => round($ratings->avg('time_management'), 2),
                'job_responsibility_avg' => round($ratings->avg('job_responsibility'), 2),
                'material_handling_avg' => round($ratings->avg('material_handling'), 2),
                'document_handling_avg' => round($ratings->avg('document_handling'), 2),
                'competency_avg' => round($ratings->avg('competency'), 2),
            ];

            $safetyWeight = 0.3;
            $otherWeight = 0.7 / 7;

            $finalScore = (
                $averages['safety_avg'] * $safetyWeight +
                $averages['communication_avg'] * $otherWeight +
                $averages['attendance_avg'] * $otherWeight +
                $averages['time_management_avg'] * $otherWeight +
                $averages['job_responsibility_avg'] * $otherWeight +
                $averages['material_handling_avg'] * $otherWeight +
                $averages['document_handling_avg'] * $otherWeight +
                $averages['competency_avg'] * $otherWeight
            );

            MonthlyStaffReport::updateOrCreate(
                [
                    'staff_id' => $stafProfile->id,
                    'month' => $month,
                    'year' => $year,
                ],
                array_merge($averages, ['final_score' => round($finalScore, 2)])
            );
        }

        return back()->with('success', 'Rating submitted successfully.');
    }

    public function monthlyReport(MonthlyStaffReportDataTable $dataTable, Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return $dataTable->with(['month' => $month, 'year' => $year])
                        ->render('staff_ratings.monthly_report', compact('month', 'year'));
    }

    public function adminTimeline(Request $request, StaffRatingDataTable $dataTable)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return $dataTable->render('staff_ratings.admin_timeline', compact('month', 'year'));
    }

}
