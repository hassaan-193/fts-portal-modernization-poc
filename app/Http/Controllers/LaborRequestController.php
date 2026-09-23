<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Models\LaborRequest;
use App\Models\StafProfile;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\DataTables\LaborRequestDataTable;

class LaborRequestController extends Controller
{
    public function index(LaborRequestDataTable $laborRequestDataTable)
    {
        return $laborRequestDataTable->render('labor_requests.index');
    }

    public function show_labor_request_form()
    {
        return view('labor_requests.labor_form');
    }

    public function labor_request_form(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'labor_id' => 'required|exists:staf_profile,id',
            'type' => 'required',
            'advance_money' => 'nullable|numeric',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        $input = $request->all();
        $laborRequest = LaborRequest::create($input);

        try {
            $requester = StafProfile::findOrFail($laborRequest->labor_id);
            app(WhatsAppService::class)->sendLeaveRequestNotification($laborRequest, $requester, 'labor');
        } catch (\Exception $e) {
            \Log::error('Failed to send labor leave request WhatsApp notification', [
                'labor_request_id' => $laborRequest->id,
                'error'            => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', 'Your request has been submitted!');;
    }

    public function destroy($id)
    {
        $laborRequest = LaborRequest::findOrFail($id);
        if (empty($laborRequest)) {
            Flash::error(__('messages.not_found', ['model' => __('models/labor_requests.singular')]));

            return redirect()->back();
        }

        $status = $laborRequest->delete();
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/labor_requests.singular')]));
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

        // Find the labor request by ID
        $laborRequest = LaborRequest::findOrFail($id);
        // dd($laborRequest);
        // dd($request->input('status'));

        // Update the status
        $laborRequest->status = $request->input('status');
        $laborRequest->save();

        Flash::success(__('messages.updated', ['model' => __('models/labor_requests.singular')]));
        return redirect()->back();
    }
}

