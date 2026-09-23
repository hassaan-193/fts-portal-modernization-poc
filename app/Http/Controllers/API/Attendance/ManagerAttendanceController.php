<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManagerAttendanceController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // ============================================
    // GET PENDING RECORDS
    // ============================================

    /**
     * Get pending attendance records
     *
     * GET /api/v1/attendance/pending? limit=50&date=2026-01-15&labor_id=1
     */
    public function getPending(Request $request)
    {
        try {
            $limit = $request->input('limit', 50);
            $page = $request->input('page', 1);
            $date = $request->input('date');
            $laborId = $request->input('labor_id');
            $foremanId = $request->input('foreman_id');

            $query = Attendance::pending()
                ->with(['labor', 'foreman', 'approvedBy']);

            // Filter by date if provided
            if ($date) {
                $query->whereDate('attendance_date', Carbon::parse($date));
            }

            // Filter by labor if provided
            if ($laborId) {
                $query->where('labor_id', $laborId);
            }

            // Filter by foreman if provided
            if ($foremanId) {
                $query->byForeman($foremanId);
            }

            $attendances = $query->orderBy('attendance_date', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            // Transform data
            $data = $attendances->map(function ($att) {
                return [
                    'id' => $att->id,
                    'labor_id' => $att->labor_id,
                    'labor_name' => $att->labor ? $att->labor->name : 'Unknown',
                    'foreman_id' => $att->foreman_id,
                    'foreman_name' => $att->foreman ? $att->foreman->name : 'Unknown',
                    'attendance_date' => $att->attendance_date->format('Y-m-d'),
                    'status' => $att->status,
                    'marked_at' => $att->marked_at ? $att->marked_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $attendances->total(),
                    'per_page' => $attendances->perPage(),
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                ],
                'message' => 'Pending records fetched successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('getPending error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending records',
            ], 500);
        }
    }

    // ============================================
    // APPROVE ATTENDANCE
    // ============================================

    /**
     * Approve attendance record
     *
     * POST /api/v1/attendance/{id}/approve
     * {
     *     "notes": "Approved by manager"
     * }
     */
    public function approve(Request $request, $attendanceId)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'notes' => 'nullable|string|max:500',
            ]);

            // Find attendance record
            $attendance = Attendance::findOrFail($attendanceId);
            $manager = $request->user();

            // Check if pending
            if (! $attendance->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending attendance can be approved',
                ], 422);
            }

            // Approve
            $attendance->approve($manager, $validated['notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Attendance approved successfully',
                'data' => [
                    'id' => $attendance->id,
                    'status' => $attendance->status,
                    'approved_at' => $attendance->approved_at->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);

        } catch (\Exception $e) {
            Log:: error('approve error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve attendance',
            ], 500);
        }
    }

    // ============================================
    // REJECT ATTENDANCE
    // ============================================

    /**
     * Reject attendance record
     *
     * POST /api/v1/attendance/{id}/reject
     * {
     *     "reason": "Invalid submission"
     * }
     */
    public function reject(Request $request, $attendanceId)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            // Find attendance record
            $attendance = Attendance:: findOrFail($attendanceId);
            $manager = $request->user();

            // Check if pending
            if (!$attendance->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending attendance can be rejected',
                ], 422);
            }

            // Reject
            $attendance->reject($manager, $validated['reason']);

            return response()->json([
                'success' => true,
                'message' => 'Attendance rejected successfully',
                'data' => [
                    'id' => $attendance->id,
                    'status' => $attendance->status,
                    'approved_at' => $attendance->approved_at->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('reject error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject attendance',
            ], 500);
        }
    }

    // ============================================
    // GET APPROVED RECORDS
    // ============================================

    /**
     * Get approved attendance records
     *
     * GET /api/v1/attendance/approved?limit=50&start_date=2026-01-01&end_date=2026-01-31
     */
    public function getApproved(Request $request)
    {
        try {
            $limit = $request->input('limit', 50);
            $page = $request->input('page', 1);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $laborId = $request->input('labor_id');

            $query = Attendance::approved()
                ->with(['labor', 'foreman', 'approvedBy']);

            // Filter by date range if provided
            if ($startDate && $endDate) {
                $query->whereBetween('attendance_date', [
                    Carbon::parse($startDate),
                    Carbon::parse($endDate),
                ]);
            }

            // Filter by labor if provided
            if ($laborId) {
                $query->where('labor_id', $laborId);
            }

            $attendances = $query->orderBy('attendance_date', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            // Transform data
            $data = $attendances->map(function ($att) {
                return [
                    'id' => $att->id,
                    'labor_id' => $att->labor_id,
                    'labor_name' => $att->labor ? $att->labor->name : 'Unknown',
                    'foreman_name' => $att->foreman ?  $att->foreman->name :  'Unknown',
                    'attendance_date' => $att->attendance_date->format('Y-m-d'),
                    'status' => $att->status,
                    'approved_by' => $att->approvedBy ? $att->approvedBy->name : null,
                    'approved_at' => $att->approved_at ? $att->approved_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $attendances->total(),
                    'per_page' => $attendances->perPage(),
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                ],
                'message' => 'Approved records fetched successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('getApproved error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approved records',
            ], 500);
        }
    }
}