<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\DataTables\StafProfileDataTable;
use App\Http\Controllers\AppBaseController;
use App\Repositories\StafProfileRepository;
use App\Http\Requests\CreateStafProfileRequest;
use App\Http\Requests\UpdateStafProfileRequest;
use App\DataTables\StafDateDataTable;
use App\Models\StaffRequest;
use App\Models\StafProfile;
use App\Services\WhatsAppService;
use App\DataTables\StaffRequestDataTable;
use App\DataTables\StafExpiryDataTable;

class StafProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /** @var  StafProfileRepository */
    private $stafprofileRepository;

    public function __construct(StafProfileRepository $stafprofileRepo)
    {
        $this->middleware('can:stafprofile');
        $this->stafprofileRepository = $stafprofileRepo;
    }

    public function index(StafProfileDataTable $ProfileDataTable)
    {
        return $ProfileDataTable->render('staf_profiles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('staf_profiles.create');
    }

   /**
     * Store a newly created Quotation in storage.
     *
     * @param CreateStafProfileRequest $request
     *
     * @return Response
     */
    public function store(CreateStafProfileRequest $request)
    {
        $input = $request->all();
        $staf = $this->stafprofileRepository->create($input);
        Flash::success(__('messages.saved', ['model' => __('models/stafprofile.singular')]));
        return redirect(route('staf.index'));


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $profile = $this->stafprofileRepository->find($id);

        if (empty($profile)) {
            Flash::error(__('models/stafprofile.singular').' '.__('messages.not_found'));
            return redirect(route('staf_profiles.index'));
        }
        $profile->load('tickets');
        $profile->load('letters');

        return view('staf_profiles.show', compact('profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $profile = $this->stafprofileRepository->find($id);

        if (empty($profile)) {
            Flash::error(__('messages.not_found', ['model' => __('models/stafprofile.singular')]));

            return redirect(route('staf.index'));
        }

        return view('staf_profiles.edit')->with('profile', $profile);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $profile = $this->stafprofileRepository->find($id);

        if (empty($profile)) {
            Flash::error(__('messages.not_found', ['model' => __('models/stafprofile.singular')]));

            return redirect(route('staf.index'));
        }
        $data = $request->all();
        $data['exclude_from_expiry'] = $request->has('exclude_from_expiry') ? 1 : 0;
        $this->stafprofileRepository->update($data, $id);

        Flash::success(__('messages.updated', ['model' => __('models/stafprofile.singular')]));

        return redirect(route('staf.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $profile = $this->stafprofileRepository->find($id);

        if (empty($profile)) {
            Flash::error(__('messages.not_found', ['model' => __('models/stafprofile.singular')]));

            return redirect(route('staf.index'));
        }

        $status = $this->stafprofileRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/stafprofile.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('staf.index'));
    }
    public function date(StafDateDataTable $DateDataTable)
    {
        return $DateDataTable->render('staf_dates.index');
    }
    public function date_delete($id)
    {
        $status = $this->stafprofileRepository->delete_date($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/stafdates.front')]));
        else
            Flash::error(__('messages.permisssion_error'));
        return redirect()->back();
    }

    // Staff Requests Related Functions
    public function staff_requests(StaffRequestDataTable $staffRequestDataTable)
    {
        return $staffRequestDataTable->render('staf_profiles.requests');
    }

    public function show_staff_request_form($id)
    {
        return view('staf_profiles.request_form')->with('staff_id', $id);
    }

    public function staff_request_form(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'staf_id' => 'required|exists:staf_profile,id',
            'type' => 'required',
            'advance_money' => 'nullable|numeric',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        $input = $request->all();
        $staffRequest = StaffRequest::create($input);

        try {
            $requester = StafProfile::findOrFail($staffRequest->staf_id);
            app(WhatsAppService::class)->sendLeaveRequestNotification($staffRequest, $requester, 'staff');
        } catch (\Exception $e) {
            \Log::error('Failed to send staff leave request WhatsApp notification', [
                'staff_request_id' => $staffRequest->id,
                'error'            => $e->getMessage(),
            ]);
        }

        Flash::success('Your request has been submitted!');

        return redirect(route('staf.index'));
    }

    public function destroy_staff_request($id)
    {
        $staffRequest = StaffRequest::findOrFail($id);
        if (empty($staffRequest)) {
            Flash::error('Staff request not found');

            return redirect()->back();
        }

        $status = $staffRequest->delete();
        if($status)
            Flash::success('Staff request deleted successfully');
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect()->back();
    }

    public function update_status(Request $request, $id)
    {
        // Validate the status input
        $request->validate([
            'status' => 'required|in:1,2',
        ]);

        // Find the staff request by ID
        $staffRequest = StaffRequest::findOrFail($id);

        // Update the status
        $staffRequest->status = $request->input('status');
        $staffRequest->save();

        Flash::success('Status updated successfully!');
        return redirect()->back();
    }

    public function expiryList(StafExpiryDataTable $dataTable)
    {
        return $dataTable->render('staf_profiles.expiry_list');
    }

}
