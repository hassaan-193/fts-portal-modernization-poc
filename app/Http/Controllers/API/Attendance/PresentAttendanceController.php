<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StafProfile;
use App\Models\Site;
use App\Models\AttendanceLaborDetail;
use App\Models\QrStaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PresentAttendanceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getTodayLabors(Request $request)
    {
        try {
            $foreman = $request->user(); 
            // Get yesterday's date (since we're marking yesterday's overtime today)
            $yesterday = Carbon::yesterday()->toDateString();
            // Get labor IDs that are already marked as present yesterday
            $markedLaborIds = AttendanceLaborDetail::with(['attendance'])
                ->whereHas('attendance', function ($q) use ($foreman, $yesterday) {
                    $q->where('foreman_id', $foreman->id)
                      ->whereDate('attendance_date', $yesterday);
                })
                ->pluck('labor_id')
                ->toArray();
            // Fetch all labors except those already marked yesterday
            $labors = StafProfile::select('id', 'name', 'last_name')
                ->where('staf_type', 'labor')
                ->whereNotIn('id', $markedLaborIds)
                ->get();
            // Fetch all sites
            $sites = Site::select('id', 'site_name')->get();
            return response()->json([
                'success' => true,
                'data' => [
                    'labors' => $labors,
                    'sites' => $sites,
                ],
                'message' => 'Data fetched successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance data',
            ]);
        }
    }

    public function submitAttendance(Request $request)
    {

        $data = $request->validate([
            'attendance_date' => 'required|date',
            'site_id' => 'nullable|integer|required_without:custom_site_name',
            'custom_site_name' => 'nullable|string|required_without:site_id',
            'labors' => 'required|array',
            'labors.*.labor_id' => 'required|integer|exists:staf_profile,id',
            'labors.*.overtime_hours' => 'nullable|numeric',
        ]);
        try {
            $attendance = Attendance::create([
                'foreman_id' => auth()->id(),
                'attendance_date' => $data['attendance_date'],
                'status' => 'pending',
            ]);

            foreach ($data['labors'] as $labor) {
                $laborId = $labor['labor_id'];
                $overtimeHours = $labor['overtime_hours'] ?? 0;

                AttendanceLaborDetail::create([
                    'attendance_id' => $attendance->id,
                    'labor_id' => $laborId,
                    'overtime_hours' => $overtimeHours,
                    'site_id' => $data['site_id'],
                    'custom_site_name' => $data['custom_site_name'],
                ]);

                // If there's overtime and no corresponding QR record, create a placeholder QR record
                if ($overtimeHours > 0) {
                    $qrExists = QrStaffAttendance::where('staff_id', $laborId)
                        ->whereDate('attendance_date', $data['attendance_date'])
                        ->exists();

                    if (!$qrExists) {
                        $dayStart = Carbon::parse($data['attendance_date'])->startOfDay();

                        QrStaffAttendance::create([
                            'staff_id' => $laborId,
                            'scanned_by' => auth()->id(),
                            'site_id' => $data['site_id'],
                            'custom_site_name' => $data['custom_site_name'],
                            'attendance_date' => $data['attendance_date'],
                            'check_in_time' => $dayStart,
                            'check_out_time' => $dayStart,
                            'duration_minutes' => 0,
                            'overtime_minutes' => 0,
                            'shift_end_time' => QrStaffAttendance::DEFAULT_SHIFT_END,
                            'status' => QrStaffAttendance::STATUS_CHECKED_OUT,
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Attendance submitted successfully!']);
        } catch (\Exception $e) {
            \Log::error('Error in submitAttendance:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return response()->json(['success' => false, 'message' => 'Failed to submit attendance.'], 500);
        }
    }
    
    public function getHistory(Request $request)
    {
        try {
            $foreman = $request->user();

            $laborDetails = AttendanceLaborDetail::with(['labor:id,name,last_name', 'site:id,site_name'])
                ->whereHas('attendance', function ($q) use ($foreman) {
                    $q->where('foreman_id', $foreman->id);
                })
                ->get();

            $records = $laborDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'labor_id' => $detail->labor_id,
                    'labor_name' => $detail->labor->name . ' ' . $detail->labor->last_name,
                    'site_id' => $detail->site_id,
                    'site_name' => $detail->site->site_name ?? $detail->custom_site_name,
                    'overtime_hours' => $detail->overtime_hours,
                    'date' => $detail->attendance->attendance_date->format('Y-m-d'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $records,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getHistory:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch records.',
            ], 500);
        }
    }
}