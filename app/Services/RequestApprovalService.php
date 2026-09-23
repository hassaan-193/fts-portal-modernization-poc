<?php

namespace App\Services;

use App\User;
use App\Models\LaborRequest;
use App\Models\RequestApproval;
use App\Models\StaffRequest;

/**
 * Two-person approval rules for Staff Requests and Labor Requests.
 *
 * The approver roster is driven entirely by spatie permissions:
 *
 *   approve_staff_requests -> required approvers on every Staff Request
 *   approve_labor_requests -> required approvers on every Labor Request
 *
 * EVERY holder of the relevant permission must approve before a request becomes
 * Approved. A single rejection marks it Rejected. Anything else is Under Review.
 * The outcome is written back onto the parent request's existing `status` column
 * (0 = Under Review, 1 = Approved, 2 = Rejected) so screens that already read
 * `status` keep working unchanged.
 *
 * Assign the roster from /users by ticking the "Staff Request Approver" and/or
 * "Labor Request Approver" role — no code change needed.
 */
class RequestApprovalService
{
    const STATUS_UNDER_REVIEW = 0;
    const STATUS_APPROVED     = 1;
    const STATUS_REJECTED     = 2;

    /**
     * A request can never reach Approved unless at least this many people hold the
     * relevant permission. Guards against a half-configured roster silently letting
     * one person single-handedly approve.
     */
    const MIN_APPROVERS = 2;

    /**
     * request type => permission that makes a user a required approver for it.
     */
    const PERMISSIONS = [
        RequestApproval::TYPE_STAFF => 'approve_staff_requests',
        RequestApproval::TYPE_LABOR => 'approve_labor_requests',
    ];

    public static function types()
    {
        return array_keys(static::PERMISSIONS);
    }

    public static function isValidType($type)
    {
        return array_key_exists($type, static::PERMISSIONS);
    }

    public static function permission($type)
    {
        return static::PERMISSIONS[$type] ?? null;
    }

    public static function typeLabel($type)
    {
        return $type === RequestApproval::TYPE_LABOR ? 'Labor' : 'Staff';
    }

    /**
     * Everyone holding the approval permission for a type, i.e. the required
     * approver roster. Resolved through spatie's scope so it covers the permission
     * held directly or via any role.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function roster($type)
    {
        static $cache = [];

        if (!array_key_exists($type, $cache)) {
            try {
                $cache[$type] = User::permission(static::permission($type))->orderBy('name')->get();
            } catch (\Exception $e) {
                // Permission row missing (migration not run yet) — treat as no roster.
                $cache[$type] = collect();
            }
        }

        return $cache[$type];
    }

    /**
     * Required approver user ids for a type.
     */
    public static function approverIds($type)
    {
        return static::roster($type)->pluck('id')->map('intval')->values()->all();
    }

    /**
     * Whether the roster is large enough for the two-person rule to mean anything.
     * While this is false, requests of this type can be rejected but can never be
     * marked Approved.
     */
    public static function isRosterValid($type)
    {
        return count(static::approverIds($type)) >= static::MIN_APPROVERS;
    }

    /**
     * The request types the given user may open the approvals screen for.
     */
    public static function typesForUser($user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return [];
        }

        return array_values(array_filter(static::types(), function ($type) use ($user) {
            return $user->can(static::permission($type));
        }));
    }

    /**
     * Whether a user is on the required approver roster for a type — the
     * authorization boundary for actually recording a decision.
     */
    public static function isApprover($type, $userId)
    {
        return static::isValidType($type)
            && in_array((int) $userId, static::approverIds($type), true);
    }

    /**
     * Fresh query builder for the underlying request model of a type.
     */
    public static function query($type)
    {
        return $type === RequestApproval::TYPE_LABOR
            ? LaborRequest::query()->with('labor')
            : StaffRequest::query()->with('staf');
    }

    public static function requesterName($type, $record)
    {
        $profile = $type === RequestApproval::TYPE_LABOR ? $record->labor : $record->staf;

        return $profile ? $profile->name : '—';
    }

    /**
     * All standing decisions on a request, keyed by approver user id.
     */
    public static function decisionsFor($type, $requestId)
    {
        return RequestApproval::where('request_type', $type)
            ->where('request_id', $requestId)
            ->get()
            ->keyBy('user_id');
    }

    /**
     * Record (or overwrite) one approver's decision, then re-derive the status.
     *
     * @return int the new status of the parent request
     */
    public static function record($type, $requestId, $userId, $decision, $note = null)
    {
        RequestApproval::updateOrCreate(
            [
                'request_type' => $type,
                'request_id'   => (int) $requestId,
                'user_id'      => (int) $userId,
            ],
            [
                'decision'   => (int) $decision,
                'note'       => $note,
                'decided_at' => now(),
            ]
        );

        return static::recalculate($type, $requestId);
    }

    /**
     * Re-derive a request's status from its decisions and persist it.
     *
     *   any roster member rejected                       -> Rejected
     *   every roster member approved (roster big enough) -> Approved
     *   otherwise                                        -> Under Review
     *
     * @return int the new status
     */
    public static function recalculate($type, $requestId)
    {
        $approverIds = static::approverIds($type);
        $decisions   = static::decisionsFor($type, $requestId);

        $status = static::STATUS_UNDER_REVIEW;

        $rejected = $decisions->first(function ($approval) use ($approverIds) {
            return $approval->decision == RequestApproval::DECISION_REJECTED
                && in_array((int) $approval->user_id, $approverIds, true);
        });

        if ($rejected) {
            $status = static::STATUS_REJECTED;
        } elseif (static::isRosterValid($type)) {
            $approvedCount = 0;
            foreach ($approverIds as $approverId) {
                $approval = $decisions->get($approverId);
                if ($approval && $approval->decision == RequestApproval::DECISION_APPROVED) {
                    $approvedCount++;
                }
            }

            if ($approvedCount === count($approverIds)) {
                $status = static::STATUS_APPROVED;
            }
        }

        $record = $type === RequestApproval::TYPE_LABOR
            ? LaborRequest::find($requestId)
            : StaffRequest::find($requestId);

        if ($record && (int) $record->status !== $status) {
            $record->status = $status;
            $record->save();
        }

        return $status;
    }

    /**
     * Per-approver progress for display: who is required, what they decided.
     *
     * @return array<int, array{label:string,user_id:int,decision:?int,note:?string,decided_at:?string}>
     */
    public static function progress($type, $requestId, $decisions = null)
    {
        $decisions = $decisions ?: static::decisionsFor($type, $requestId);

        $progress = [];
        foreach (static::roster($type) as $approver) {
            $approval = $decisions->get((int) $approver->id);

            $progress[] = [
                'label'      => $approver->name,
                'user_id'    => (int) $approver->id,
                'decision'   => $approval ? (int) $approval->decision : null,
                'note'       => $approval ? $approval->note : null,
                'decided_at' => $approval && $approval->decided_at
                    ? $approval->decided_at->format('d-m-Y H:i')
                    : null,
            ];
        }

        return $progress;
    }

    /**
     * Flatten one request record into the shape the datatable and detail view use.
     */
    public static function row($type, $record, $decisions = null)
    {
        $decisions = $decisions ?: static::decisionsFor($type, $record->id);
        $own       = $decisions->get((int) auth()->id());

        return [
            'id'            => (int) $record->id,
            'type'          => $type,
            'type_label'    => static::typeLabel($type),
            'requester'     => static::requesterName($type, $record),
            'request_type'  => $record->type,
            'start_date'    => $record->start_date,
            'end_date'      => $record->end_date,
            'advance_money' => $record->advance_money,
            'note'          => $record->note,
            'created_at'    => $record->created_at,
            'status'        => (int) $record->status,
            'progress'      => static::progress($type, $record->id, $decisions),
            'own_decision'  => $own ? (int) $own->decision : null,
            'can_decide'    => static::isApprover($type, auth()->id()),
            'sort'          => $record->getAttributes()['created_at'] ?? '',
        ];
    }

    /**
     * Merged, newest-first feed of requests across the given types.
     *
     * @param  array   $types   subset of static::types()
     * @param  string  $filter  'pending' (Under Review only) or 'all'
     * @param  int     $perType cap applied per type when $filter is 'all'
     * @return \Illuminate\Support\Collection
     */
    public static function feed(array $types, $filter = 'pending', $perType = 300)
    {
        $rows = collect();

        foreach ($types as $type) {
            if (!static::isValidType($type)) {
                continue;
            }

            $query = static::query($type);

            if ($filter === 'pending') {
                $query->where('status', static::STATUS_UNDER_REVIEW);
            } else {
                $query->limit($perType);
            }

            $records = $query->orderBy('id', 'desc')->get();

            if ($records->isEmpty()) {
                continue;
            }

            $decisionsByRequest = RequestApproval::where('request_type', $type)
                ->whereIn('request_id', $records->pluck('id')->all())
                ->get()
                ->groupBy('request_id');

            foreach ($records as $record) {
                $decisions = ($decisionsByRequest->get($record->id) ?: collect())->keyBy('user_id');
                $rows->push(static::row($type, $record, $decisions));
            }
        }

        return $rows->sortByDesc('sort')->values();
    }

    /**
     * Bootstrap-badge markup for one request's approval progress.
     */
    public static function progressBadges(array $progress)
    {
        if (empty($progress)) {
            return '<span class="badge badge-secondary">no approvers assigned</span>';
        }

        $html = '';
        foreach ($progress as $slot) {
            if ($slot['decision'] === RequestApproval::DECISION_APPROVED) {
                $cls = 'success';
                $txt = 'approved';
            } elseif ($slot['decision'] === RequestApproval::DECISION_REJECTED) {
                $cls = 'danger';
                $txt = 'disapproved';
            } else {
                $cls = 'warning';
                $txt = 'pending';
            }

            $html .= '<span class="badge badge-' . $cls . ' d-block text-left mb-1">'
                . e($slot['label']) . ': ' . $txt . '</span>';
        }

        return $html;
    }

    /**
     * Bootstrap-badge markup for a derived status value.
     */
    public static function statusBadge($status)
    {
        $map = [
            static::STATUS_APPROVED     => ['Approved', 'success'],
            static::STATUS_REJECTED     => ['Rejected', 'danger'],
            static::STATUS_UNDER_REVIEW => ['Under Review', 'warning'],
        ];

        list($msg, $cls) = $map[(int) $status] ?? ['Unknown', 'secondary'];

        return '<span class="badge badge-' . $cls . '">' . $msg . '</span>';
    }
}
