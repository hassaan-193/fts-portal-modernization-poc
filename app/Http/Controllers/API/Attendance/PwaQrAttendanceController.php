<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\QrStaffAttendance;
use App\Models\StafProfile;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PwaQrAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Scan a staff member's QR code for check-in or check-out.
     *
     * POST /api/v1/pwa-qr-attendance/scan
     */
    public function scan(Request $request)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'nullable|integer|exists:staf_profile,id',
                'qr_token' => 'nullable|string|required_without:staff_id',
                'site_id' => 'nullable|integer|exists:sites,id',
                'custom_site_name' => 'nullable|string|max:255',
            ]);

            $scanner = $request->user();
            $staffId = $validated['staff_id'] ?? null;

            if (!$staffId && !empty($validated['qr_token'])) {
                $staffId = $this->extractStaffIdFromToken($validated['qr_token']);
            }

            if (!$staffId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR format. Please scan a valid staff QR.',
                ], 422);
            }
            $today = Carbon::today();

            $staff = StafProfile::find($staffId);
            $staffName = trim($staff->name . ' ' . ($staff->last_name ?? ''));

            $openRecord = QrStaffAttendance::forStaff($staffId)
                ->onDate($today)
                ->checkedIn()
                ->first();

            if ($openRecord) {
                if (!$openRecord->canCheckOut()) {
                    $elapsed = Carbon::parse($openRecord->check_in_time)->diffInSeconds(now());
                    $remaining = QrStaffAttendance::MIN_CHECKOUT_SECONDS - $elapsed;

                    return response()->json([
                        'success' => false,
                        'message' => "Too soon to check out {$staffName}. Please wait {$remaining} more seconds.",
                    ], 400);
                }

                $openRecord->check_out_time = now();
                $openRecord->status = QrStaffAttendance::STATUS_CHECKED_OUT;
                $openRecord->calculateDuration();
                $openRecord->calculateOvertime();
                $openRecord->save();

                return response()->json([
                    'success' => true,
                    'action' => 'check_out',
                    'message' => "{$staffName} has been checked out successfully.",
                    'data' => [
                        'id' => $openRecord->id,
                        'staff_id' => $openRecord->staff_id,
                        'staff_name' => $staffName,
                        'check_in_time' => $openRecord->check_in_time->format('h:i A'),
                        'check_out_time' => $openRecord->check_out_time->format('h:i A'),
                        'duration_minutes' => $openRecord->duration_minutes,
                        'hours_worked' => QrStaffAttendance::formatMinutes($openRecord->duration_minutes),
                        'overtime_minutes' => $openRecord->overtime_minutes,
                        'overtime_display' => QrStaffAttendance::formatMinutes($openRecord->overtime_minutes),
                    ],
                ]);
            }

            $record = QrStaffAttendance::create([
                'staff_id' => $staffId,
                'scanned_by' => $scanner->id,
                'site_id' => $validated['site_id'] ?? null,
                'custom_site_name' => $validated['custom_site_name'] ?? null,
                'attendance_date' => $today,
                'check_in_time' => now(),
                'shift_end_time' => QrStaffAttendance::DEFAULT_SHIFT_END,
                'status' => QrStaffAttendance::STATUS_CHECKED_IN,
            ]);

            return response()->json([
                'success' => true,
                'action' => 'check_in',
                'message' => "{$staffName} has been checked in successfully.",
                'data' => [
                    'id' => $record->id,
                    'staff_id' => $record->staff_id,
                    'staff_name' => $staffName,
                    'check_in_time' => $record->check_in_time->format('h:i A'),
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('PwaQrAttendanceController@scan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing scan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lookup a staff member by ID (from QR content).
     *
     * GET /api/v1/pwa-qr-attendance/lookup-staff/{staffId}
     */
    public function lookupStaff($staffId)
    {
        try {
            $staff = StafProfile::find($staffId);

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found. Invalid QR code.',
                ], 404);
            }

            $today = Carbon::today();
            $openRecord = QrStaffAttendance::forStaff($staffId)
                ->onDate($today)
                ->checkedIn()
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $staff->id,
                    'name' => trim($staff->name . ' ' . ($staff->last_name ?? '')),
                    'staf_type' => $staff->staf_type,
                    'has_open_session' => $openRecord !== null,
                    'check_in_time' => $openRecord ? $openRecord->check_in_time->format('h:i A') : null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PwaQrAttendanceController@lookupStaff error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error looking up staff',
            ], 500);
        }
    }

    /**
     * Get today's attendance records for the current scanner's site.
     *
     * GET /api/v1/pwa-qr-attendance/today
     */
    public function todayRecords(Request $request)
    {
        try {
            $scanner = $request->user();
            $today = Carbon::today();

            $records = QrStaffAttendance::with(['staff', 'site'])
                ->where('scanned_by', $scanner->id)
                ->onDate($today)
                ->orderBy('check_in_time', 'desc')
                ->get();

            $data = $records->map(function ($record) {
                $workedMinutes = $record->duration_minutes;
                if (!$workedMinutes && $record->isOpen()) {
                    $workedMinutes = (int) Carbon::parse($record->check_in_time)->diffInMinutes(now());
                }

                return [
                    'id' => $record->id,
                    'staff_id' => $record->staff_id,
                    'staff_name' => trim($record->staff->name . ' ' . ($record->staff->last_name ?? '')),
                    'site_name' => $record->site ? $record->site->site_name : ($record->custom_site_name ?? 'N/A'),
                    'check_in_time' => $record->check_in_time->format('h:i A'),
                    'check_out_time' => $record->check_out_time ? $record->check_out_time->format('h:i A') : null,
                    'duration_minutes' => $record->duration_minutes,
                    'hours_worked' => QrStaffAttendance::formatMinutes($workedMinutes),
                    'overtime_minutes' => $record->overtime_minutes,
                    'overtime_display' => QrStaffAttendance::formatMinutes($record->overtime_minutes),
                    'status' => $record->status,
                ];
            });

            $checkedIn = $records->where('status', QrStaffAttendance::STATUS_CHECKED_IN)->count();
            $checkedOut = $records->where('status', QrStaffAttendance::STATUS_CHECKED_OUT)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'records' => $data,
                    'summary' => [
                        'total' => $records->count(),
                        'checked_in' => $checkedIn,
                        'checked_out' => $checkedOut,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PwaQrAttendanceController@todayRecords error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching records',
            ], 500);
        }
    }

    /**
     * Get available sites for the dropdown.
     *
     * GET /api/v1/pwa-qr-attendance/sites
     */
    public function getSites()
    {
        try {
            $sites = Site::select('id', 'site_name')->orderBy('site_name')->get();

            return response()->json([
                'success' => true,
                'data' => $sites,
            ]);

        } catch (\Exception $e) {
            Log::error('PwaQrAttendanceController@getSites error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sites',
            ], 500);
        }
    }

    private function extractStaffIdFromToken($token)
    {
        $trimmed = trim($token);

        if (preg_match('/^\d+$/', $trimmed)) {
            return (int) $trimmed;
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if (!empty($decoded['staff_id']) && is_numeric($decoded['staff_id'])) {
                return (int) $decoded['staff_id'];
            }
            if (!empty($decoded['id']) && is_numeric($decoded['id'])) {
                return (int) $decoded['id'];
            }
        }

        if (preg_match('/staff[_-]?id[=:]\s*(\d+)/i', $trimmed, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
