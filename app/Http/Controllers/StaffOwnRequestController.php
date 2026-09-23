<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\StaffRequest;
use App\Models\StafProfile;
use App\Services\WhatsAppService;
use App\DataTables\OwnStaffRequestDataTable;

class StaffOwnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function authorizeOwnRequestAccess()
    {
        $user = auth()->user();
        if ($user && ($user->can('stafprofile') || $user->can('own_request_form'))) {
            return;
        }
        abort(403, 'Unauthorized.');
    }

    private function requireStaffProfileId()
    {
        $staffProfileId = auth()->user()->staf_profile_id;
        if (!$staffProfileId) {
            abort(403, 'No staff profile is linked to your account. Contact an administrator.');
        }
        return $staffProfileId;
    }

    public function index(OwnStaffRequestDataTable $dataTable)
    {
        $this->authorizeOwnRequestAccess();
        $this->requireStaffProfileId();

        return $dataTable->render('staf_profiles.own_requests_index');
    }

    public function create()
    {
        $this->authorizeOwnRequestAccess();
        $staffProfileId = $this->requireStaffProfileId();

        return view('staf_profiles.own_request_form')->with('staff_id', $staffProfileId);
    }

    public function store(Request $request)
    {
        $this->authorizeOwnRequestAccess();
        $staffProfileId = $this->requireStaffProfileId();

        $validator = \Validator::make($request->all(), [
            'type' => 'required',
            'advance_money' => 'nullable|numeric',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        // Never trust a client-supplied staf_id — always the server-resolved one.
        $input = $request->except(['staf_id']);
        $input['staf_id'] = $staffProfileId;
        $staffRequest = StaffRequest::create($input);

        try {
            $requester = StafProfile::findOrFail($staffRequest->staf_id);
            app(WhatsAppService::class)->sendLeaveRequestNotification($staffRequest, $requester, 'staff');
        } catch (\Exception $e) {
            \Log::error('Failed to send own staff leave request WhatsApp notification', [
                'staff_request_id' => $staffRequest->id,
                'error'            => $e->getMessage(),
            ]);
        }

        Flash::success('Your request has been submitted!');

        return redirect()->route('own_requests.index');
    }
}
