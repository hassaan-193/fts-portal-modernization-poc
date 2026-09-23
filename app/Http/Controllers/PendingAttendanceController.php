<?php

namespace App\Http\Controllers;

use App\DataTables\PendingAttendanceDataTable;
use App\DataTables\PresentsAttendanceDataTable;
use App\Models\Attendance;
use App\Models\AttendanceApproval;
use App\Models\StafProfile;
use App\Exports\PresentsOvertimeExport;
use App\Exports\AbsentAttendanceExport;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class PendingAttendanceController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->middleware('auth');
    }

    public function index(PendingAttendanceDataTable $dataTable)
    {
        // Get all laborers for the filter dropdown
        $laborers = StafProfile::orderBy('name', 'asc')->get();
        
        return $dataTable->render('pending-attendance.index', ['laborers' => $laborers]);
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'informed' => 'required|in:informed,uninformed',
            'specific_reason' => 'required_if:informed,informed|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'status' => 'approved', 
            'approved_by' => auth()->user()->id, 
            'approved_at' => now(), 
        ]);

        AttendanceApproval::create([
            'attendance_id' => $attendance->id,
            'approved_by' => auth()->user()->id, 
            'action' => 'approved',
            'informed' => $validated['informed'],
            'specific_reason' => $validated['specific_reason'] ?? null,
            'reason' => null, 
        ]);

        // Send WhatsApp notification if uninformed and approved
        if ($validated['informed'] === 'uninformed') {
            try {
                $staffProfile = $attendance->labor;

                if ($staffProfile && $staffProfile->mobile_no) {
                    Log::info('Attempting to send WhatsApp for uninformed approval', [
                        'attendance_id' => $attendance->id,
                        'staff_id' => $staffProfile->id,
                        'mobile' => $staffProfile->mobile_no
                    ]);

                    $this->whatsAppService->sendAttendanceApprovalNotification(
                        $staffProfile->mobile_no,
                        $staffProfile->name . ' ' . $staffProfile->last_name
                    );

                    Log::info('WhatsApp message sent successfully for uninformed approval', [
                        'attendance_id' => $attendance->id,
                        'staff_id' => $staffProfile->id,
                        'staff_name' => $staffProfile->name . ' ' . $staffProfile->last_name
                    ]);
                } else {
                    Log::warning('Staff profile or mobile missing for WhatsApp notification', [
                        'attendance_id' => $attendance->id,
                        'has_profile' => $staffProfile ? true : false,
                        'has_mobile' => $staffProfile && $staffProfile->mobile_no ? true : false
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp sending failed for uninformed approval', [
                    'attendance_id' => $attendance->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Don't throw exception - approval is already recorded
                // Just log the WhatsApp failure
            }
        }

        return redirect()->back()->with('success', 'Attendance approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'informed' => 'required|in:informed,uninformed',
            'specific_reason' => 'required_if:informed,informed|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'status' => 'rejected',
            'approved_by' => auth()->user()->id,
            'approved_at' => now(),
            'notes' => $validated['reason'],
        ]);

        AttendanceApproval::create([
            'attendance_id' => $attendance->id,
            'approved_by' => auth()->user()->id,
            'action' => 'rejected',
            'reason' => $validated['reason'],
            'informed' => $validated['informed'],
            'specific_reason' => $validated['specific_reason'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Attendance rejected successfully.');
    }

    public function presentsIndex(PresentsAttendanceDataTable $dataTable)
    {
        // Get the query and process through DataTable
        $model = new Attendance();
        $collection = $dataTable->query($model);
        $tableData = $dataTable->dataTable($collection);
        
        // Convert to JSON response format
        $response = $tableData->toJson();
        $data = json_decode($response->getContent(), true);
        
        // Get all laborers for the filter dropdown
        $laborers = StafProfile::orderBy('name', 'asc')->get();
        
        return $dataTable->render('pending-attendance.presents-index', [
            'tableData' => $data,
            'laborers' => $laborers
        ]);
    }

    public function data(PendingAttendanceDataTable $dataTable)
    {
        return $dataTable->render('pending-attendance.index');
    }

    public function presentsData(PresentsAttendanceDataTable $dataTable)
    {
        return $dataTable->render('pending-attendance.presents-index');
    }

    public function presentsReportExport(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $laborId = $request->input('labor_id');

        // Build query
        $query = \DB::table('attendances')
            ->join('attendance_labor_details', 'attendances.id', '=', 'attendance_labor_details.attendance_id')
            ->join('staf_profile', 'attendance_labor_details.labor_id', '=', 'staf_profile.id')
            ->leftJoin('users', 'attendances.foreman_id', '=', 'users.id')
            ->leftJoin('sites', 'attendance_labor_details.site_id', '=', 'sites.id')
            ->select(
                'attendances.id',
                'attendances.attendance_date',
                'staf_profile.name as labor_name',
                'staf_profile.id as labor_id',
                'users.name as foreman_name',
                \DB::raw('COALESCE(sites.site_name, attendance_labor_details.custom_site_name) as site_name'),
                'attendance_labor_details.overtime_hours'
            )
            ->whereNotNull('attendance_labor_details.overtime_hours')
            ->whereNotNull('attendance_labor_details.labor_id')
            ->orderBy('attendances.attendance_date', 'desc');

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('attendances.attendance_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('attendances.attendance_date', '<=', $dateTo);
        }

        // Apply labor filter
        if ($laborId) {
            $query->where('attendance_labor_details.labor_id', $laborId);
        }

        $data = $query->get();

        // Prepare filters array for display in report
        $filters = [];
        $laborName = '';

        if ($dateFrom) {
            $filters['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $filters['date_to'] = $dateTo;
        }

        if ($laborId) {
            $labor = StafProfile::find($laborId);
            if ($labor) {
                $laborName = $labor->name;
                $filters['labor_name'] = $labor->name;
            }
        }

        // Generate filename
        $filename = 'Overtime_Report_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new PresentsOvertimeExport($data, $filters), $filename);
    }

    public function absentReportExport(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $laborId = $request->input('labor_id');
        $status = $request->input('status');
        $informed = $request->input('informed');

        // Build query
        $query = Attendance::with(['labor', 'foreman', 'approvals'])
            ->orderBy('attendance_date', 'desc');

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('attendance_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('attendance_date', '<=', $dateTo);
        }

        // Apply labor filter
        if ($laborId) {
            $query->where('labor_id', $laborId);
        }

        // Always filter for approved status
        $query->where('status', 'approved');

        // Apply informed filter if needed
        if ($informed) {
            $query->whereHas('approvals', function ($q) use ($informed) {
                $q->where('informed', $informed);
            });
        }

        $data = $query->get();

        // Debug logging
        \Log::info('absentReportExport - Total records: ' . $data->count());
        \Log::info('absentReportExport - Date From: ' . $dateFrom);
        \Log::info('absentReportExport - Date To: ' . $dateTo);
        \Log::info('absentReportExport - Labor ID: ' . $laborId);

        // Prepare filters array for display in report
        $filters = [];

        if ($dateFrom) {
            $filters['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $filters['date_to'] = $dateTo;
        }

        if ($laborId) {
            $labor = StafProfile::find($laborId);
            if ($labor) {
                $filters['labor_name'] = $labor->name;
            }
        }

        if ($status) {
            $filters['status'] = $status;
        }

        if ($informed) {
            $filters['informed'] = $informed;
        }

        // Generate filename
        $filename = 'Absent_Attendance_Report_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new AbsentAttendanceExport($data, $filters), $filename);
    }
}