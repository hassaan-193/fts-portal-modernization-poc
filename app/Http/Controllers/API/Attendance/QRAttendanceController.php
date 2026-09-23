<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\QRCode;
use App\Models\StafProfile;
use App\Services\QRValidationService;
use App\Services\AttendanceSessionService;
use App\Services\AttendanceEvaluationService;
use App\Services\GeoValidationService;
use App\Services\ShiftRuleService;
use App\User;
use App\Exports\UserAttendanceExport;
use App\Exports\AllUsersAttendanceExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

/**
 * QRAttendanceController
 * 
 * Handles QR + GeoLocation-based attendance scanning.
 * Endpoints for clock-in/clock-out with QR code validation and geo-fence checking.
 */
class QRAttendanceController extends Controller
{
    protected $qrValidationService;
    protected $attendanceSessionService;
    protected $attendanceEvaluationService;
    protected $geoValidationService;
    protected $shiftRuleService;

    public function __construct(
        QRValidationService $qrValidationService,
        AttendanceSessionService $attendanceSessionService,
        AttendanceEvaluationService $attendanceEvaluationService,
        GeoValidationService $geoValidationService,
        ShiftRuleService $shiftRuleService
    ) {
        $this->qrValidationService = $qrValidationService;
        $this->attendanceSessionService = $attendanceSessionService;
        $this->attendanceEvaluationService = $attendanceEvaluationService;
        $this->geoValidationService = $geoValidationService;
        $this->shiftRuleService = $shiftRuleService;
        // Only apply auth:api middleware to API methods, not web report methods
        $this->middleware('auth:api')->only([
            'scanQR', 'getTodayAttendance', 'getAttendanceSummary', 
            'getOpenSession', 'evaluateDay', 'getShiftRules'
        ]);
    }

    /**
     * Scan QR code for clock-in/clock-out
     *
     * POST /api/v1/qr-attendance/scan
     * {
     *     "qr_token": "a1b2c3d4e5f6...",
     *     "latitude": 25.2048,
     *     "longitude": 55.2708
     * }
     * 
     * Note: Office location coordinates are configured in .env
     */
    public function scanQR(Request $request)
    {
        try {
            // Get authenticated user (the staff member scanning)
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'qr_token' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            // Validate QR code token
            $qrValidation = $this->qrValidationService->validateToken($validated['qr_token']);
            if (!$qrValidation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $qrValidation['message'],
                ], 400);
            }

            // Process QR scan
            $result = $this->attendanceSessionService->processQRScan(
                $user->id,
                $validated['latitude'],
                $validated['longitude']
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

            // Get today's sessions for summary
            $todaysSessions = $this->attendanceSessionService->getTodaysSessionsForUser($user->id);
            $totalHours = $this->attendanceSessionService->calculateTotalHours($todaysSessions);

            return response()->json([
                'success' => true,
                'action' => $result['action'],
                'message' => $result['message'],
                'session' => [
                    'id' => $result['session']->id,
                    'clock_in' => $result['session']->clock_in_time->format('Y-m-d H:i:sP'),
                    'clock_out' => $result['session']->clock_out_time ? $result['session']->clock_out_time->format('Y-m-d H:i:sP') : null,
                    'duration_minutes' => $result['session']->duration_minutes,
                    'shift' => $result['session']->shift_window,
                    'is_late' => $result['session']->is_late,
                ],
                'today_summary' => [
                    'total_sessions' => $todaysSessions->count(),
                    'total_hours' => $totalHours['formatted'],
                    'total_minutes' => $totalHours['total_minutes'],
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@scanQR error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR scan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's attendance sessions for authenticated user
     *
     * GET /api/v1/qr-attendance/today
     */
    public function getTodayAttendance()
    {
        try {
            $user = auth()->user();
            
            $sessions = $this->attendanceSessionService->getTodaysSessionsForUser($user->id);
            $totalHours = $this->attendanceSessionService->calculateTotalHours($sessions);
            $isAbsent = $sessions->isEmpty();

            $sessionDetails = $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'clock_in' => $session->clock_in_time->format('Y-m-d H:i:sP'),
                    'clock_out' => $session->clock_out_time ? $session->clock_out_time->format('Y-m-d H:i:sP') : 'Open',
                    'duration_minutes' => $session->duration_minutes,
                    'shift' => $session->shift_window,
                    'is_late' => $session->is_late,
                ];
            });

            return response()->json([
                'success' => true,
                'user_name' => $user->name,
                'date' => Carbon::today()->format('Y-m-d'),
                'is_absent' => $isAbsent,
                'sessions' => $sessionDetails,
                'total_sessions' => $sessions->count(),
                'total_hours' => $totalHours['formatted'],
                'total_minutes' => $totalHours['total_minutes'],
            ], 200);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getTodayAttendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching today\'s attendance',
            ], 500);
        }
    }

    /**
     * Get detailed attendance summary for a staff member
     *
     * GET /api/v1/qr-attendance/summary/{staff_id}?date=2026-02-25
     */
    public function getAttendanceSummary($staffId, Request $request)
    {
        try {
            $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
            
            $staff = StafProfile::findOrFail($staffId);
            $summary = $this->attendanceEvaluationService->getDetailedSummary($staff, $date);

            return response()->json([
                'success' => true,
                'data' => $summary,
            ], 200);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getAttendanceSummary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance summary',
            ], 500);
        }
    }

    /**
     * Get open session (if any) for authenticated user
     *
     * GET /api/v1/qr-attendance/open-session
     */
    public function getOpenSession()
    {
        try {
            $user = auth()->user();
            $openSession = $this->attendanceSessionService->getOpenSessionToday($user->id);

            if (!$openSession) {
                return response()->json([
                    'success' => true,
                    'has_open_session' => false,
                    'message' => 'No open session found',
                ], 200);
            }

            return response()->json([
                'success' => true,
                'has_open_session' => true,
                'session' => [
                    'id' => $openSession->id,
                    'clock_in' => $openSession->clock_in_time->format('Y-m-d H:i:sP'),
                    'shift' => $openSession->shift_window,
                    'is_late' => $openSession->is_late,
                    'work_mode' => $openSession->work_mode,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getOpenSession error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching open session',
            ], 500);
        }
    }

    /**
     * Evaluate and generate daily attendance record
     * This should be called at end of day (via scheduled job or manual trigger)
     *
     * POST /api/v1/qr-attendance/evaluate-day
     * {
     *     "staff_id": 1,
     *     "date": "2026-02-25" (optional, defaults to today)
     * }
     */
    public function evaluateDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'required|integer|exists:staf_profile,id',
                'date' => 'nullable|date_format:Y-m-d',
            ]);

            $staff = StafProfile::findOrFail($validated['staff_id']);
            $date = $validated['date'] ? Carbon::parse($validated['date']) : Carbon::today();

            $evaluation = $this->attendanceEvaluationService->evaluateDay($staff, $date);

            if (!$evaluation['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $evaluation['message'],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'attendance_id' => $evaluation['attendance']->id,
                'status' => $evaluation['status'],
                'total_hours' => $evaluation['total_hours'],
                'has_late' => $evaluation['has_late'],
                'sessions_count' => $evaluation['sessions_count'],
                'message' => $evaluation['message'],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@evaluateDay error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error evaluating attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get shift rules/configuration
     *
     * GET /api/v1/qr-attendance/shift-rules
     */
    public function getShiftRules()
    {
        try {
            $shifts = $this->shiftRuleService->getAllShifts();

            return response()->json([
                'success' => true,
                'shifts' => $shifts,
            ], 200);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getShiftRules error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching shift rules',
            ], 500);
        }
    }

    /**
     * Get attendance report for a specific user (detailed view)
     * GET /qr-attendance/report/user/{userId}?start_date=2026-02-01&end_date=2026-02-28
     */
    public function userReport($userId, Request $request)
    {
        try {
            $user = User::findOrFail($userId);
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();

            // Get all users for the dropdown filter
            $allUsers = User::orderBy('name')->get();

            return view('qr-attendance.user-report', [
                'user' => $user,
                'allUsers' => $allUsers,
                'userId' => $userId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@userReport error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')->with('error', 'Error loading user report: ' . $e->getMessage());
        }
    }

    /**
     * Get user report data (AJAX endpoint)
     * GET /qr-attendance/api/user-report-data/{userId}?start_date=2026-02-01&end_date=2026-02-28
     */
    public function getUserReportData($userId, Request $request)
    {
        try {
            $user = User::findOrFail($userId);
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();

            // Get all sessions for the user in the date range
            $sessions = AttendanceSession::where('user_id', $userId)
                ->whereBetween('session_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('session_date', 'asc')
                ->orderBy('clock_in_time', 'asc')
                ->get();

            // Group sessions by date
            $groupedByDate = $sessions->groupBy(function ($session) {
                return $session->session_date->format('Y-m-d');
            });

            // Build daily summary data
            $dailyData = $groupedByDate->map(function ($daySessions) {
                $totalMinutes = $daySessions->sum('duration_minutes');
                $hours = floor($totalMinutes / 60);
                $mins = $totalMinutes % 60;
                $isLate = $daySessions->where('is_late', true)->count() > 0;
                $isLateShift1 = $daySessions->where('shift_window', 'shift_1')->where('is_late', true)->count() > 0;
                $isLateShift2 = $daySessions->where('shift_window', 'shift_2')->where('is_late', true)->count() > 0;

                return [
                    'date' => $daySessions->first()->session_date->format('Y-m-d'),
                    'dayName' => $daySessions->first()->session_date->format('l'),
                    'sessionCount' => $daySessions->count(),
                    'clockInTime' => $daySessions->first()->clock_in_time->format('Y-m-d H:i:sP'),
                    'clockOutTime' => $daySessions->last()->clock_out_time ? $daySessions->last()->clock_out_time->format('Y-m-d H:i:sP') : 'Open',
                    'totalMinutes' => $totalMinutes,
                    'duration' => $hours . 'h ' . $mins . 'm',
                    'shiftWindow' => $daySessions->first()->shift_window,
                    'isLate' => $isLate,
                    'isLateShift1' => $isLateShift1,
                    'isLateShift2' => $isLateShift2,
                    'sessions' => $daySessions->toArray(),
                ];
            })->values();

            // Calculate overall statistics
            $totalMinutes = $sessions->sum('duration_minutes');
            $hours = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;
            $lateDaysShift1 = $sessions->where('shift_window', 'shift_1')->where('is_late', true)
                ->groupBy(function ($s) { return $s->session_date->format('Y-m-d'); })->count();
            $lateDaysShift2 = $sessions->where('shift_window', 'shift_2')->where('is_late', true)
                ->groupBy(function ($s) { return $s->session_date->format('Y-m-d'); })->count();
            $lateDays = $lateDaysShift1 + $lateDaysShift2;

            // Absent days = Mon–Sat days in range (up to today) where user has no sessions
            $absentDays = 0;
            $today = Carbon::today();
            $rangeEnd = $endDate->copy()->lt($today) ? $endDate->copy() : $today->copy();
            $checkDate = $startDate->copy();
            while ($checkDate->lte($rangeEnd)) {
                if (!$checkDate->isSunday()) {
                    if (!$groupedByDate->has($checkDate->format('Y-m-d'))) {
                        $absentDays++;
                    }
                }
                $checkDate->addDay();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                    'summary' => [
                        'totalDays' => $groupedByDate->count(),
                        'totalSessions' => $sessions->count(),
                        'totalHours' => $hours . 'h ' . $mins . 'm',
                        'totalMinutes' => $totalMinutes,
                        'lateDays' => $lateDays,
                        'lateDaysShift1' => $lateDaysShift1,
                        'lateDaysShift2' => $lateDaysShift2,
                        'absentDays' => $absentDays,
                    ],
                    'dailyRecords' => $dailyData,
                    'startDate' => $startDate->format('Y-m-d'),
                    'endDate' => $endDate->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getUserReportData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching report data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get attendance report for all users (dashboard view)
     * GET /qr-attendance/report/all?start_date=2026-02-01&end_date=2026-02-28
     */
    public function allUsersReport(Request $request)
    {
        try {
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();

            // Get all users for the dropdown filter
            $allUsers = User::orderBy('name')->get();

            return view('qr-attendance.all-users-report', [
                'allUsers' => $allUsers,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@allUsersReport error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')->with('error', 'Error loading attendance report: ' . $e->getMessage());
        }
    }

    /**
     * Get all users report data (AJAX endpoint)
     * GET /qr-attendance/api/all-users-report-data?start_date=2026-02-01&end_date=2026-02-28&user_id=1
     */
    public function getAllUsersReportData(Request $request)
    {
        try {
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();
            $userId = $request->input('user_id');
            $lateOnly = $request->input('late_only');

            // Get all sessions in date range
            $query = AttendanceSession::whereBetween('session_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with('user')
                ->orderBy('session_date', 'desc')
                ->orderBy('clock_in_time', 'desc');

            // Filter by specific user if provided - ensure not empty
            if (!empty($userId)) {
                $query->where('user_id', (int) $userId);
            }

            // Filter by late arrivals if requested - check for string '1'
            if (!empty($lateOnly) && $lateOnly === '1') {
                $query->where('is_late', true);
            }

            $allSessions = $query->get();

            // Get all users who have sessions
            $userIds = $allSessions->pluck('user_id')->unique();
            $users = User::whereIn('id', $userIds)->get();

            // Build summary data for each user with daily consolidation
            $userSummaries = collect();
            $today = Carbon::today();
            $rangeEnd = $endDate->copy()->lt($today) ? $endDate->copy() : $today->copy();

            foreach ($users as $user) {
                $userSessions = $allSessions->where('user_id', $user->id);

                // Group by date for daily view
                $sessionsByDate = $userSessions->groupBy(function ($session) {
                    return $session->session_date->format('Y-m-d');
                });

                $lateDaysShift1 = $userSessions->where('shift_window', 'shift_1')->where('is_late', true)
                    ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
                $lateDaysShift2 = $userSessions->where('shift_window', 'shift_2')->where('is_late', true)
                    ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
                $lateDays = $lateDaysShift1 + $lateDaysShift2;

                // Absent = Mon–Sat days in range (up to today) with no sessions
                $absentDays = 0;
                $checkDate = $startDate->copy();
                while ($checkDate->lte($rangeEnd)) {
                    if (!$checkDate->isSunday()) {
                        if (!$sessionsByDate->has($checkDate->format('Y-m-d'))) {
                            $absentDays++;
                        }
                    }
                    $checkDate->addDay();
                }

                $userSummaries->push([
                    'user' => $user,
                    'totalDaysWorked' => $sessionsByDate->count(),
                    'lateDays' => $lateDays,
                    'lateDaysShift1' => $lateDaysShift1,
                    'lateDaysShift2' => $lateDaysShift2,
                    'absentDays' => $absentDays,
                    'sessions' => $userSessions,
                ]);
            }

            // Calculate overall statistics
            $totalLateShift1 = $userSummaries->sum('lateDaysShift1');
            $totalLateShift2 = $userSummaries->sum('lateDaysShift2');
            $totalLate = $userSummaries->sum('lateDays');
            $totalAbsent = $userSummaries->sum('absentDays');

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'totalEmployees' => $users->count(),
                        'totalLateShift1' => $totalLateShift1,
                        'totalLateShift2' => $totalLateShift2,
                        'totalLate' => $totalLate,
                        'totalAbsent' => $totalAbsent,
                    ],
                    'userSummaries' => $userSummaries->map(function ($summary) {
                        return [
                            'id' => $summary['user']->id,
                            'name' => $summary['user']->name,
                            'totalDaysWorked' => $summary['totalDaysWorked'],
                            'lateDays' => $summary['lateDays'],
                            'lateDaysShift1' => $summary['lateDaysShift1'],
                            'lateDaysShift2' => $summary['lateDaysShift2'],
                            'absentDays' => $summary['absentDays'],
                            'sessions' => $summary['sessions']->map(function ($session) {
                                return [
                                    'clock_in_time' => $session->clock_in_time ? $session->clock_in_time->format('Y-m-d H:i:sP') : null,
                                    'clock_out_time' => $session->clock_out_time ? $session->clock_out_time->format('Y-m-d H:i:sP') : null,
                                    'shift_window' => $session->shift_window,
                                    'is_late' => $session->is_late,
                                    'session_date' => $session->session_date->format('Y-m-d H:i:s'),
                                ];
                            })->values(),
                        ];
                    })->values(),
                    'startDate' => $startDate->format('Y-m-d'),
                    'endDate' => $endDate->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@getAllUsersReportData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching report data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export attendance report for a specific user to CSV/Excel
     * GET /qr-attendance/export/user/{userId}?format=csv&start_date=2026-02-01&end_date=2026-02-28
     */
    public function exportUserReport($userId, Request $request)
    {
        try {
            $format = $request->input('format', 'csv');
            $user = User::findOrFail($userId);
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();

            $sessions = AttendanceSession::where('user_id', $userId)
                ->whereBetween('session_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('session_date', 'asc')
                ->orderBy('clock_in_time', 'asc')
                ->get();

            if ($format === 'csv') {
                return $this->exportToCsv($user, $sessions, $startDate, $endDate);
            } else {
                return $this->exportToExcel($user, $sessions, $startDate, $endDate);
            }

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@exportUserReport error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report');
        }
    }

    /**
     * Export all users attendance report to CSV/Excel
     * GET /qr-attendance/export/all?format=csv&start_date=2026-02-01&end_date=2026-02-28
     */
    public function exportAllUsersReport(Request $request)
    {
        try {
            $format = $request->input('format', 'csv');
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::today();
            $userId = $request->input('user_id');
            $lateOnly = $request->input('late_only');

            $query = AttendanceSession::whereBetween('session_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->with('user');

            // Filter by user if specified
            if (!empty($userId)) {
                $query->where('user_id', $userId);
            }

            // Filter by late arrivals only if specified
            if (!empty($lateOnly) && $lateOnly === '1') {
                $query->where('is_late', true);
            }

            $allSessions = $query->orderBy('session_date', 'asc')
                ->orderBy('clock_in_time', 'asc')
                ->get();

            if ($format === 'csv') {
                return $this->exportAllToCsv($allSessions, $startDate, $endDate);
            } else {
                return $this->exportAllToExcel($allSessions, $startDate, $endDate);
            }

        } catch (\Exception $e) {
            Log::error('QRAttendanceController@exportAllUsersReport error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting report');
        }
    }

    /**
     * Export single user to CSV
     */
    private function exportToCsv($user, $sessions, $startDate, $endDate)
    {
        $filename = "attendance_" . $user->id . "_" . now()->format('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($user, $sessions, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add header info
            fputcsv($file, ['Attendance Report - Single User']);
            fputcsv($file, ['Employee Name', $user->name]);
            fputcsv($file, ['Employee ID', $user->id]);
            fputcsv($file, ['Report Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Add column headers
            fputcsv($file, [
                'Date',
                'Day',
                'Clock In',
                'Clock Out',
                'Shift',
                'Status',
            ]);

            // Group sessions by date, then by shift — one row per shift per day
            $groupedByDate = $sessions->groupBy(function ($s) {
                return $s->session_date->format('Y-m-d');
            });

            $today = Carbon::today();
            $rangeEnd = $endDate->copy()->lt($today) ? $endDate->copy() : $today->copy();

            // Iterate all working days (Mon–Sat) in range
            $csvCheckDate = $startDate->copy();
            while ($csvCheckDate->lte($rangeEnd)) {
                if (!$csvCheckDate->isSunday()) {
                    $csvDateStr = $csvCheckDate->format('Y-m-d');
                    if (isset($groupedByDate[$csvDateStr])) {
                        $shiftGroups = $groupedByDate[$csvDateStr]->groupBy('shift_window')->sortKeys();
                        foreach ($shiftGroups as $shiftKey => $shiftSessions) {
                            $first = $shiftSessions->sortBy('clock_in_time')->first();
                            $last  = $shiftSessions->sortBy('clock_in_time')->last();
                            $isLate = $first->is_late;
                            $isSat = $first->session_date->isSaturday();
                            $shiftLabel = $isSat ? 'Saturday' : strtoupper(str_replace('shift_', 'Shift ', $shiftKey));
                            $statusLabel = $isLate ? 'Late (' . ($shiftKey === 'shift_1' ? 'S1' : 'S2') . ')' : 'On Time';
                            fputcsv($file, [
                                $first->session_date->format('Y-m-d'),
                                $first->session_date->format('l'),
                                $first->clock_in_time->format('H:i:s'),
                                $last->clock_out_time ? $last->clock_out_time->format('H:i:s') : 'N/A',
                                $shiftLabel,
                                $statusLabel,
                            ]);
                        }
                    } else {
                        fputcsv($file, [
                            $csvDateStr,
                            $csvCheckDate->format('l'),
                            '—', '—', '—', 'Absent',
                        ]);
                    }
                }
                $csvCheckDate->addDay();
            }

            // Add summary
            fputcsv($file, []);
            $lateDaysShift1 = $sessions->where('shift_window', 'shift_1')->where('is_late', true)
                ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
            $lateDaysShift2 = $sessions->where('shift_window', 'shift_2')->where('is_late', true)
                ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
            $lateDays = $lateDaysShift1 + $lateDaysShift2;
            $absentDays = 0;
            $absCheck = $startDate->copy();
            while ($absCheck->lte($rangeEnd)) {
                if (!$absCheck->isSunday()) {
                    if (!$groupedByDate->has($absCheck->format('Y-m-d'))) {
                        $absentDays++;
                    }
                }
                $absCheck->addDay();
            }
            fputcsv($file, ['DAYS WORKED', $groupedByDate->count()]);
            fputcsv($file, ['LATE (SHIFT 1)', $lateDaysShift1]);
            fputcsv($file, ['LATE (SHIFT 2)', $lateDaysShift2]);
            fputcsv($file, ['TOTAL LATE DAYS', $lateDays]);
            fputcsv($file, ['ABSENT DAYS', $absentDays]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all users to CSV
     */
    private function exportAllToCsv($allSessions, $startDate, $endDate)
    {
        $filename = "attendance_all_users_" . now()->format('Y-m-d_H-i-s') . ".csv";
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            $file = fopen($tempPath, 'w');
            
            // Add header info
            fputcsv($file, ['Attendance Report - All Users (Grouped by Employee)']);
            fputcsv($file, ['Report Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Group sessions by user
            $sessionsByUser = $allSessions->groupBy('user_id');
            $today = Carbon::today();
            $rangeEnd = $endDate->copy()->lt($today) ? $endDate->copy() : $today->copy();

            foreach ($sessionsByUser as $userId => $userSessions) {
                $userName = $userSessions->first()->user ? $userSessions->first()->user->name : 'Unknown';

                // User section header
                fputcsv($file, []);
                fputcsv($file, ['Employee Name', $userName]);
                fputcsv($file, ['Employee ID', $userId]);
                fputcsv($file, []);

                // Column headers for this user's records
                fputcsv($file, [
                    'Date',
                    'Day',
                    'Clock In (First)',
                    'Clock Out (Last)',
                    'Shift',
                    'Status',
                ]);

                // Group by date for daily rows (one row per shift per day)
                $sessionsByDate = $userSessions
                    ->sortBy('clock_in_time')
                    ->sortBy('session_date')
                    ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'));

                // Iterate all working days (Mon–Sat) in range to include absent rows
                $csvCheckDate = $startDate->copy();
                while ($csvCheckDate->lte($rangeEnd)) {
                    if (!$csvCheckDate->isSunday()) {
                        $csvDateStr = $csvCheckDate->format('Y-m-d');
                        if (isset($sessionsByDate[$csvDateStr])) {
                            // One row per shift
                            $shiftGroups = $sessionsByDate[$csvDateStr]->groupBy('shift_window')->sortKeys();
                            foreach ($shiftGroups as $shiftKey => $shiftSessions) {
                                try {
                                    $first = $shiftSessions->sortBy('clock_in_time')->first();
                                    $last  = $shiftSessions->sortBy('clock_in_time')->last();
                                    $isLate = $first->is_late;
                                    $isSat = $first->session_date->isSaturday();
                                    $shiftLabel = $isSat ? 'Saturday' : strtoupper(str_replace('shift_', 'Shift ', $shiftKey));
                                    $statusLabel = $isLate ? 'Late (' . ($shiftKey === 'shift_1' ? 'S1' : 'S2') . ')' : 'On Time';
                                    fputcsv($file, [
                                        $first->session_date->format('Y-m-d'),
                                        $first->session_date->format('l'),
                                        $first->clock_in_time ? $first->clock_in_time->format('H:i:s') : 'N/A',
                                        $last->clock_out_time ? $last->clock_out_time->format('H:i:s') : 'N/A',
                                        $shiftLabel,
                                        $statusLabel,
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::error('Error processing session for export: ' . $e->getMessage());
                                    continue;
                                }
                            }
                        } else {
                            // Absent row
                            fputcsv($file, [
                                $csvDateStr,
                                $csvCheckDate->format('l'),
                                '—', '—', '—', 'Absent',
                            ]);
                        }
                    }
                    $csvCheckDate->addDay();
                }

                // Per-user summary
                $lateDaysShift1 = $userSessions->where('shift_window', 'shift_1')->where('is_late', true)
                    ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
                $lateDaysShift2 = $userSessions->where('shift_window', 'shift_2')->where('is_late', true)
                    ->groupBy(fn ($s) => $s->session_date->format('Y-m-d'))->count();
                $lateDays = $lateDaysShift1 + $lateDaysShift2;
                $absentDaysUser = 0;
                $absCheckDate = $startDate->copy();
                while ($absCheckDate->lte($rangeEnd)) {
                    if (!$absCheckDate->isSunday() && !$sessionsByDate->has($absCheckDate->format('Y-m-d'))) {
                        $absentDaysUser++;
                    }
                    $absCheckDate->addDay();
                }
                fputcsv($file, []);
                fputcsv($file, ['Days Worked', $sessionsByDate->count()]);
                fputcsv($file, ['Late (Shift 1)', $lateDaysShift1]);
                fputcsv($file, ['Late (Shift 2)', $lateDaysShift2]);
                fputcsv($file, ['Total Late Days', $lateDays]);
                fputcsv($file, ['Absent Days', $absentDaysUser]);
                fputcsv($file, ['---', '---', '---', '---', '---', '---']);
            }

            fclose($file);
            
            // Download the file
            return response()->download($tempPath, $filename, [
                'Content-Type' => 'text/csv; charset=utf-8',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('CSV Export Error: ' . $e->getMessage());
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            throw $e;
        }
    }

    /**
     * Export single user to styled Excel
     */
    private function exportToExcel($user, $sessions, $startDate, $endDate)
    {
        $filename = "attendance_{$user->id}_" . now()->format('Y-m-d_H-i-s') . ".xlsx";
        return Excel::download(
            new UserAttendanceExport($user, $sessions, $startDate, $endDate),
            $filename
        );
    }

    /**
     * Export all users to styled Excel
     */
    private function exportAllToExcel($allSessions, $startDate, $endDate)
    {
        $filename = "attendance_all_users_" . now()->format('Y-m-d_H-i-s') . ".xlsx";
        return Excel::download(
            new AllUsersAttendanceExport($allSessions, $startDate, $endDate),
            $filename
        );
    }
}
