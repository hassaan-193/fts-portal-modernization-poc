<?php

namespace App\Http\Controllers\API\Attendance;
use App\Mail\AttendanceSubmitted;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StafProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;



class ForemanAttendanceController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // ============================================
    // GET TODAY'S LABORS
    // ============================================

    /**
     * Get today's assigned labors
     *
     * GET /api/v1/attendance/today
     */
    public function getTodayLabors(Request $request)
    {
        try {
            $foreman = $request->user();
            $today = Carbon::today();

            // Get all staf profiles (assuming they are assigned to foreman)
            $labors = StafProfile::select('id', 'name','last_name', 'staf_type')
                ->where('staf_type', 'labor')
                ->get();


            // Transform to match frontend expectations
            $laborData = $labors->map(function ($labor) {
                return [
                    'id' => $labor->id,
                    'name' => $labor->name,
                    'last_name' => $labor->last_name,
                    'full_name' => trim($labor->name . ' ' . $labor->last_name),
                    'role' => $labor->staf_type ?? 'Labor',
                    'avatar' => $labor->image ? asset('storage/' . $labor->image) : null,
                ];
            });

            // Check if already submitted today
            $submittedToday = Attendance::where('foreman_id', $foreman->id)
                ->whereDate('attendance_date', $today)
                ->exists();

            return response()->json([
                'success' => true,
                'data' => [
                    'labors' => $laborData,
                    'submitted_today' => $submittedToday,
                    'date' => $today->format('Y-m-d'),
                    'count' => $laborData->count(),
                ],
                'message' => 'Labors fetched successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('getTodayLabors error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch labors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // SUBMIT ATTENDANCE
    // ============================================

    /**
     * Submit attendance (mark absent labors)
     *
     * POST /api/v1/attendance/submit
     * {
     *     "absent_labor_ids": [1, 2, 3],
     *     "date": "2026-01-15"
     * }
     */
public function submitAttendance(Request $request)
{
    Log::info('Attendance submission started');

    try {
        // ✅ Validate request
        $validated = $request->validate([
            'absent_labor_ids' => 'nullable|array',
            'absent_labor_ids.*' => 'integer|exists:staf_profile,id',
            'date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
        ]);

        $foreman = $request->user();
        $date = !empty($validated['date'])
            ? Carbon::parse($validated['date'])
            : Carbon::today();

        $absentIds = $validated['absent_labor_ids'] ?? [];

        DB::beginTransaction();

        $count = 0;
        foreach ($absentIds as $laborId) {
            Attendance::updateOrCreate(
                [
                    'labor_id' => $laborId,
                    'attendance_date' => $date->format('Y-m-d'),
                ],
                [
                    'foreman_id' => $foreman->id,
                    'status' => Attendance::STATUS_PENDING,
                    'marked_at' => now(),
                ]
            );
            $count++;
        }

        DB::commit();

        // ✅ Fetch absent labor details
        $absentLabors = StafProfile::whereIn('id', $absentIds)->get();

        /*
        |--------------------------------------------------------------------------
        | BUILD FINAL EMAIL MESSAGE
        |--------------------------------------------------------------------------
        */
        $message = "Attendance Todays Record\n\n";

        $message .= "Absent Labors (" . $absentLabors->count() . "):\n";

        if ($absentLabors->count()) {
            foreach ($absentLabors as $labor) {
                $message .= "- {$labor->name} (ID: {$labor->id})\n";
            }
        } else {
            $message .= "- None (All labors are present)\n";
        }

        $message .= "\nSubmitted At:\n";
        $message .= "- " . now()->format('Y-m-d H:i:s') . "\n";

        /*
        |--------------------------------------------------------------------------
        | SEND EMAIL
        |--------------------------------------------------------------------------
        */
        Mail::raw($message, function ($mail) {
            $mail->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->to([
                'kirankumar.rak@example.com',
                'Saad.rak@example.com',
                'prasad.rak@example.com',
                'Jalaa.rak@example.com',
                'secretary@example.com',
                'shamaeel@example.com',

            ])
            ->subject('Attendance Updates | ' . now()->format('Y-m-d'));
        });


        Log::info('Attendance email sent successfully');

        return response()->json([
            'success' => true,
            'message' => 'Attendance submitted successfully',
            'data' => [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ],
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('submitAttendance failed', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to submit attendance',
        ], 500);
    }
}



    // ============================================
    // CHECK IF SUBMITTED
    // ============================================

    /**
     * Check if attendance already submitted today
     *
     * GET /api/v1/attendance/check-submitted
     */
    public function checkSubmitted(Request $request)
    {
        try {
            $foreman = $request->user();
            $today = Carbon::today();

            $submitted = Attendance::where('foreman_id', $foreman->id)
                ->whereDate('attendance_date', $today)
                ->exists();

            return response()->json([
                'success' => true,
                'submitted' => $submitted,
                'date' => $today->format('Y-m-d'),
                'message' => 'Check completed',
            ], 200);

        } catch (\Exception $e) {
            Log::error('checkSubmitted error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check submission status',
            ], 500);
        }
    }

    // ============================================
    // GET HISTORY
    // ============================================

    /**
     * Get attendance history for foreman
     *
     * GET /api/v1/attendance/history? limit=30&page=1
     */
    public function getHistory(Request $request)
    {
        try {
            $foreman = $request->user();
            $limit = $request->input('limit', 30);
            $page = $request->input('page', 1);

            $attendances = Attendance::where('foreman_id', $foreman->id)
                ->with(['labor', 'approvedBy'])
                ->orderBy('attendance_date', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            // Transform data
            $data = $attendances->map(function ($att) {
                return [
                    'id' => $att->id,
                    'labor_id' => $att->labor_id,
                    'labor_name' => $att->labor ?  $att->labor->name : 'Unknown',
                    'attendance_date' => $att->attendance_date->format('Y-m-d'),
                    'status' => $att->status,
                    'marked_at' => $att->marked_at ? $att->marked_at->format('Y-m-d H:i:s') : null,
                    'approved_at' => $att->approved_at ? $att->approved_at->format('Y-m-d H:i:s') : null,
                    'approved_by' => $att->approvedBy ? $att->approvedBy->name : null,
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
                'message' => 'History fetched successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('getHistory error: ' .$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch history',
            ], 500);
        }
    }
}