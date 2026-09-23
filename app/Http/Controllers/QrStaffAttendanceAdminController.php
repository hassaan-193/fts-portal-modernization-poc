<?php

namespace App\Http\Controllers;

use App\Models\QrStaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class QrStaffAttendanceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $summary = DB::table('qr_staff_attendances')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN review_status = "pending" THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN review_status = "approved" THEN 1 ELSE 0 END) as approved')
            ->selectRaw('SUM(CASE WHEN review_status = "rejected" THEN 1 ELSE 0 END) as rejected')
            ->first();

        return view('attendance.qr-staff-overtime', [
            'summary' => $summary,
            'filters' => $request->only(['date_from', 'date_to', 'review_status', 'staff_query']),
        ]);
    }

    public function data(Request $request)
    {
        $query = $this->buildRecordsQuery($request);

        return DataTables::of($query)
            ->filter(function ($builder) use ($request) {
                $search = trim((string) data_get($request->input('search'), 'value', ''));
                if ($search === '') {
                    return;
                }

                $builder->where(function ($q) use ($search) {
                    $q->where('sp.name', 'like', '%' . $search . '%')
                        ->orWhere('sp.last_name', 'like', '%' . $search . '%')
                        ->orWhere('sp.id', 'like', '%' . $search . '%')
                        ->orWhere('s.site_name', 'like', '%' . $search . '%')
                        ->orWhere('qsa.custom_site_name', 'like', '%' . $search . '%');
                });
            }, true)
            ->editColumn('attendance_date', function ($row) {
                return \Carbon\Carbon::parse($row->attendance_date)->format('Y-m-d');
            })
            ->addColumn('staff', function ($row) {
                $fullName = trim(($row->staff_first_name ?? '') . ' ' . ($row->staff_last_name ?? '')) ?: 'N/A';
                $staffId = e($row->staff_id ?? '-');

                return e($fullName) . '<br><small class="text-muted">ID: ' . $staffId . '</small>';
            })
            ->editColumn('check_in_time', function ($row) {
                return $row->check_in_time ? \Carbon\Carbon::parse($row->check_in_time)->format('H:i') : '-';
            })
            ->editColumn('check_out_time', function ($row) {
                if (!$row->check_out_time) {
                    return '<span class="badge badge-info">Still in</span>';
                }

                return \Carbon\Carbon::parse($row->check_out_time)->format('H:i');
            })
            ->addColumn('hours_worked_col', function ($row) {
                if ($row->duration_minutes) {
                    return QrStaffAttendance::formatMinutes((int) $row->duration_minutes);
                }

                if ($row->check_in_time && !$row->check_out_time) {
                    $minutes = (int) \Carbon\Carbon::parse($row->check_in_time)->diffInMinutes(now());

                    return QrStaffAttendance::formatMinutes($minutes)
                        . ' <small class="text-muted">(ongoing)</small>';
                }

                return QrStaffAttendance::formatMinutes(0);
            })
            ->addColumn('manual_hours', function ($row) {
                $manual = $this->resolveManualOvertimeForQrRow($row->staff_id, $row->attendance_date, $row->id);

                $html = QrStaffAttendance::formatDecimalHours($manual['hours']);
                if ($manual['matched_date'] && $manual['label']) {
                    $html .= '<br><small class="text-muted" title="Manual OT from PWA for ' . e($manual['matched_date']) . '">'
                        . e($manual['matched_date']) . '</small>';
                }

                return $html;
            })
            ->addColumn('qr_hours', function ($row) {
                if (!$row->check_out_time) {
                    return '<span class="text-muted">—</span>';
                }

                return QrStaffAttendance::formatMinutes((int) $row->overtime_minutes);
            })
            ->addColumn('final_hours', function ($row) {
                if ($row->final_overtime_minutes === null) {
                    return '<span class="badge badge-secondary">NOT SET</span>';
                }

                $html = '<span class="badge badge-primary">'
                    . e(QrStaffAttendance::formatMinutes((int) $row->final_overtime_minutes)) . '</span>';
                $html .= '<br><small class="text-muted">' . e(strtoupper((string) $row->final_overtime_source)) . '</small>';

                if (!empty($row->finalizer_name)) {
                    $html .= '<br><small class="text-muted">' . e($row->finalizer_name) . '</small>';
                }

                return $html;
            })
            ->addColumn('review_badge', function ($row) {
                $badgeClass = 'badge-secondary';
                if ($row->review_status === QrStaffAttendance::REVIEW_APPROVED) {
                    $badgeClass = 'badge-success';
                } elseif ($row->review_status === QrStaffAttendance::REVIEW_REJECTED) {
                    $badgeClass = 'badge-danger';
                }

                $html = '<span class="badge ' . $badgeClass . '">' . e(strtoupper((string) $row->review_status)) . '</span>';
                if (!empty($row->reviewer_name)) {
                    $html .= '<br><small class="text-muted">' . e($row->reviewer_name) . '</small>';
                }

                return $html;
            })
            ->addColumn('decision_form', function ($row) {
                return $this->buildDecisionFormHtml($row);
            })
            ->rawColumns([
                'staff',
                'check_out_time',
                'hours_worked_col',
                'manual_hours',
                'qr_hours',
                'final_hours',
                'review_badge',
                'decision_form',
            ])
            ->only([
                'id',
                'attendance_date',
                'staff',
                'site_name',
                'check_in_time',
                'check_out_time',
                'hours_worked_col',
                'manual_hours',
                'qr_hours',
                'final_hours',
                'review_badge',
                'decision_form',
            ])
            ->toJson();
    }

    public function review(Request $request, $id)
    {
        $record = QrStaffAttendance::findOrFail($id);

        if ($record->review_status === QrStaffAttendance::REVIEW_APPROVED) {
            flash()->error('This record is already approved and cannot be changed.');

            return redirect()->back();
        }

        $data = $request->validate([
            'review_status' => 'required|in:approved,rejected,pending',
            'final_overtime_source' => 'required|in:manual,qr,custom',
            'custom_overtime_hours' => 'nullable|required_if:final_overtime_source,custom|numeric|min:0|max:24',
            'final_decision_notes' => 'nullable|string|max:1000',
        ]);
        $record->review_status = $data['review_status'];
        if ($data['review_status'] === QrStaffAttendance::REVIEW_PENDING) {
            $record->reviewed_by = null;
            $record->reviewed_at = null;
        } else {
            $record->reviewed_by = auth()->id();
            $record->reviewed_at = now();
        }

        $source = $data['final_overtime_source'];
        $manualMinutes = (int) round($this->resolveManualOvertimeForQrRow($record->staff_id, $record->attendance_date, $record->id)['hours'] * 60);
        $qrMinutes = (int) $record->overtime_minutes;

        if ($source === QrStaffAttendance::FINAL_SOURCE_MANUAL) {
            $finalMinutes = $manualMinutes;
        } elseif ($source === QrStaffAttendance::FINAL_SOURCE_QR) {
            $finalMinutes = $qrMinutes;
        } else {
            $customHours = (float) ($data['custom_overtime_hours'] ?? 0);
            $finalMinutes = (int) round($customHours * 60);
        }

        $record->final_overtime_source = $source;
        $record->final_overtime_minutes = $finalMinutes;
        $record->finalized_by = auth()->id();
        $record->finalized_at = now();
        $record->final_decision_notes = $data['final_decision_notes'] ?? null;

        $record->save();

        return redirect()->back()->with('success', 'QR attendance review and final overtime decision updated successfully.');
    }

    /**
     * Resolve manual OT for a QR row. PWA present attendance saves OT against
     * yesterday's date while QR scan uses the scan day — match same day first,
     * then the previous calendar day.
     */
    private function resolveManualOvertimeForQrRow($staffId, $attendanceDate, $qrRecordId): array
    {
        $qrDate = Carbon::parse($attendanceDate)->startOfDay();

        $sameDayHours = $this->sumManualOvertimeForDate($staffId, $qrDate);
        if ($sameDayHours > 0) {
            return [
                'hours' => $sameDayHours,
                'matched_date' => $qrDate->toDateString(),
                'label' => null,
            ];
        }

        $previousDay = $qrDate->copy()->subDay();
        $previousDayHours = $this->sumManualOvertimeForDate($staffId, $previousDay);
        if ($previousDayHours > 0) {
            return [
                'hours' => $previousDayHours,
                'matched_date' => $previousDay->toDateString(),
                'label' => 'prev_day',
            ];
        }

        // PWA logs OT on an earlier date than the QR scan day — attach to the first QR row on/after that OT date.
        $recentManual = DB::table('attendance_labor_details as ald')
            ->join('attendances as a', 'a.id', '=', 'ald.attendance_id')
            ->where('ald.labor_id', $staffId)
            ->whereDate('a.attendance_date', '<=', $qrDate->toDateString())
            ->whereDate('a.attendance_date', '>=', $qrDate->copy()->subDays(7)->toDateString())
            ->where('ald.overtime_hours', '>', 0)
            ->orderByDesc('a.attendance_date')
            ->selectRaw('DATE(a.attendance_date) as matched_date, SUM(COALESCE(ald.overtime_hours, 0)) as hours')
            ->groupBy(DB::raw('DATE(a.attendance_date)'))
            ->first();

        if ($recentManual) {
            $anchorQrId = DB::table('qr_staff_attendances')
                ->where('staff_id', $staffId)
                ->whereDate('attendance_date', '>=', $recentManual->matched_date)
                ->orderBy('attendance_date')
                ->orderBy('id')
                ->value('id');

            if ((int) $anchorQrId !== (int) $qrRecordId) {
                return [
                    'hours' => 0,
                    'matched_date' => null,
                    'label' => null,
                ];
            }

            return [
                'hours' => (float) $recentManual->hours,
                'matched_date' => $recentManual->matched_date,
                'label' => 'recent',
            ];
        }

        return [
            'hours' => 0,
            'matched_date' => null,
            'label' => null,
        ];
    }

    private function sumManualOvertimeForDate($staffId, Carbon $date): float
    {
        return (float) DB::table('attendance_labor_details as ald')
            ->join('attendances as a', 'a.id', '=', 'ald.attendance_id')
            ->where('ald.labor_id', $staffId)
            ->whereDate('a.attendance_date', $date->toDateString())
            ->sum(DB::raw('COALESCE(ald.overtime_hours, 0)'));
    }

    private function buildRecordsQuery(Request $request)
    {
        $query = DB::table('qr_staff_attendances as qsa')
            ->leftJoin('staf_profile as sp', 'sp.id', '=', 'qsa.staff_id')
            ->leftJoin('sites as s', 's.id', '=', 'qsa.site_id')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'qsa.reviewed_by')
            ->leftJoin('users as finalizer', 'finalizer.id', '=', 'qsa.finalized_by')
            ->select([
                'qsa.id',
                'qsa.attendance_date',
                'qsa.check_in_time',
                'qsa.check_out_time',
                'qsa.duration_minutes',
                'qsa.overtime_minutes',
                'qsa.review_status',
                'qsa.final_overtime_source',
                'qsa.final_overtime_minutes',
                'qsa.final_decision_notes',
                'qsa.staff_id',
                'sp.name as staff_first_name',
                'sp.last_name as staff_last_name',
                DB::raw('COALESCE(s.site_name, qsa.custom_site_name, "N/A") as site_name'),
                'reviewer.name as reviewer_name',
                'finalizer.name as finalizer_name',
            ])
            ->orderByDesc('qsa.attendance_date')
            ->orderByDesc('qsa.id');

        if ($request->filled('date_from')) {
            $query->whereDate('qsa.attendance_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('qsa.attendance_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('review_status')) {
            $query->where('qsa.review_status', $request->input('review_status'));
        }

        if ($request->filled('staff_query')) {
            $q = trim($request->input('staff_query'));
            $query->where(function ($builder) use ($q) {
                $builder->where('sp.name', 'like', '%' . $q . '%')
                    ->orWhere('sp.last_name', 'like', '%' . $q . '%')
                    ->orWhere('sp.id', 'like', '%' . $q . '%');
            });
        }

        return $query;
    }

    private function buildDecisionFormHtml($row)
    {
        $isLocked = $row->review_status === QrStaffAttendance::REVIEW_APPROVED;
        $disabled = $isLocked ? ' disabled' : '';
        $customVisibleStyle = $row->final_overtime_source === 'custom' ? '' : 'display:none;';
        $customValue = $row->final_overtime_source === 'custom' && $row->final_overtime_minutes !== null
            ? number_format($row->final_overtime_minutes / 60, 2, '.', '')
            : '';

        $reviewOptions = [
            'pending' => 'Pending',
            'approved' => 'Approve',
            'rejected' => 'Reject',
        ];
        $sourceOptions = [
            'manual' => 'Use Manual',
            'qr' => 'Use QR',
            'custom' => 'Custom',
        ];

        $reviewSelectOptions = '';
        foreach ($reviewOptions as $value => $label) {
            $selected = $row->review_status === $value ? ' selected' : '';
            $reviewSelectOptions .= '<option value="' . e($value) . '"' . $selected . '>' . e($label) . '</option>';
        }

        $sourceSelectOptions = '';
        foreach ($sourceOptions as $value => $label) {
            $selected = $row->final_overtime_source === $value ? ' selected' : '';
            $sourceSelectOptions .= '<option value="' . e($value) . '"' . $selected . '>' . e($label) . '</option>';
        }

        $saveButton = $isLocked ? '' : '<button type="submit" class="btn btn-primary btn-sm">Save</button>';
        $formAction = route('attendance.qr-overtime.review', $row->id);
        $csrf = csrf_field();
        $notesValue = e((string) ($row->final_decision_notes ?? ''));

        return '
            <form method="POST" action="' . e($formAction) . '">
                ' . $csrf . '
                <div class="d-flex">
                    <select name="review_status" class="form-control form-control-sm mr-1"' . $disabled . '>
                        ' . $reviewSelectOptions . '
                    </select>
                    ' . $saveButton . '
                </div>
                <div class="d-flex mt-1">
                    <select name="final_overtime_source" class="form-control form-control-sm mr-1" onchange="toggleCustomHours(this)"' . $disabled . '>
                        ' . $sourceSelectOptions . '
                    </select>
                    <div class="custom-hours-wrapper" style="' . e($customVisibleStyle) . '">
                        <input
                            type="number"
                            step="0.25"
                            min="0"
                            max="24"
                            name="custom_overtime_hours"
                            class="form-control form-control-sm"
                            placeholder="Custom hrs"
                            value="' . e($customValue) . '"' . $disabled . '
                        >
                    </div>
                </div>
                <input type="text" name="final_decision_notes" class="form-control form-control-sm mt-1" placeholder="Optional notes" value="' . $notesValue . '"' . $disabled . '>
            </form>
        ';
    }
}

