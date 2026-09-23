<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\RequestApproval;
use App\DataTables\RequestApprovalDataTable;
use App\Services\RequestApprovalService;

/**
 * The approvers' workspace: incoming Staff/Labor requests awaiting a decision.
 *
 * Access is permission-driven — `approve_staff_requests` and `approve_labor_requests`
 * (see the "Staff Request Approver" / "Labor Request Approver" roles). Holding a
 * permission both opens the screen for that side AND puts the user on the required
 * approver roster for it.
 */
class RequestApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * The request types the current user may act on, or a 403.
     */
    private function typesForCurrentUser()
    {
        $types = RequestApprovalService::typesForUser();

        if (empty($types)) {
            abort(403, 'You do not have permission to approve requests.');
        }

        return $types;
    }

    public function index(RequestApprovalDataTable $dataTable, Request $request)
    {
        $types  = $this->typesForCurrentUser();
        $filter = $request->get('filter') === 'all' ? 'all' : 'pending';

        return $dataTable->render('request_approvals.index', [
            'filter' => $filter,
            'types'  => $types,
        ]);
    }

    /**
     * Full detail for one request, including the requester's note and every
     * approver's decision.
     */
    public function show($type, $id)
    {
        $types = $this->typesForCurrentUser();

        if (!RequestApprovalService::isValidType($type) || !in_array($type, $types, true)) {
            abort(403, 'You do not have permission to view this request.');
        }

        $record = RequestApprovalService::query($type)->find($id);
        if (!$record) {
            abort(404);
        }

        return view('request_approvals.show', [
            'row' => RequestApprovalService::row($type, $record),
        ]);
    }

    public function approve(Request $request, $type, $id)
    {
        return $this->decide($request, $type, $id, RequestApproval::DECISION_APPROVED);
    }

    public function disapprove(Request $request, $type, $id)
    {
        return $this->decide($request, $type, $id, RequestApproval::DECISION_REJECTED);
    }

    /**
     * Record this approver's decision on one request and re-derive its status.
     */
    private function decide(Request $request, $type, $id, $decision)
    {
        $this->typesForCurrentUser();

        if (!RequestApprovalService::isValidType($type)) {
            abort(404);
        }

        // Roster membership is the authorization boundary — never trust the posted type/id alone.
        if (!RequestApprovalService::isApprover($type, auth()->id())) {
            abort(403, 'You are not an approver for this request type.');
        }

        $record = RequestApprovalService::query($type)->find($id);
        if (!$record) {
            Flash::error('Request not found.');

            return redirect()->back();
        }

        $status = RequestApprovalService::record(
            $type,
            $record->id,
            auth()->id(),
            $decision,
            $request->input('note')
        );

        $outcome = [
            RequestApprovalService::STATUS_APPROVED     => 'fully approved',
            RequestApprovalService::STATUS_REJECTED     => 'rejected',
            RequestApprovalService::STATUS_UNDER_REVIEW => 'still under review, waiting on the other approver',
        ];

        Flash::success('Your decision was saved. This request is now ' . $outcome[$status] . '.');

        return redirect()->back();
    }
}
