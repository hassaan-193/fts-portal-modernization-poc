<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Models\StafDates;
use App\Models\StafProfile;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\StafDateDataTable;
use App\Http\Controllers\AppBaseController;
use App\Repositories\StafDateRepository;
use App\Http\Requests\CreateStafDateRequest;
use App\Http\Requests\UpdateStafDateRequest;
use Illuminate\Support\Facades\Log;

class StafDateController extends AppBaseController
{
    /** @var  StafDateRepository */
    private $stafDateRepository;

    public function __construct(StafDateRepository $stafDateRepository)
    {
        // $this->middleware('can:stafprofile');
        $this->stafDateRepository = $stafDateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param StafDateDataTable $dataTable
     * @return Response
     */
    public function index(StafDateDataTable $dataTable)
    {
        return $dataTable->render('staf_dates.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $staffMembers = StafProfile::orderBy('name', 'asc')->get();
        return view('staf_dates.create', compact('staffMembers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateStafDateRequest $request
     * @return Response
     */
    public function store(CreateStafDateRequest $request)
    {
        try {
            $input = $request->all();
            $this->stafDateRepository->create($input);
            
            Flash::success(__('messages.saved', ['model' => __('models/stafdates.singular')]));
            return redirect(route('staf-dates.index'));
        } catch (\Exception $e) {
            Log::error('StafDateController@store error: ' . $e->getMessage());
            Flash::error('Error saving staff date: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $stafDate = $this->stafDateRepository->find($id);

        if (empty($stafDate)) {
            Flash::error(__('messages.not_found', ['model' => __('models/stafdates.singular')]));
            return redirect(route('staf-dates.index'));
        }

        $stafDate->load('staff');
        return view('staf_dates.show', compact('stafDate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $stafDate = $this->stafDateRepository->find($id);

        if (empty($stafDate)) {
            Flash::error(__('messages.not_found', ['model' => __('models/stafdates.singular')]));
            return redirect(route('staf-dates.index'));
        }

        $staffMembers = StafProfile::orderBy('name', 'asc')->get();
        return view('staf_dates.edit', compact('stafDate', 'staffMembers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStafDateRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateStafDateRequest $request, $id)
    {
        try {
            $stafDate = $this->stafDateRepository->find($id);

            if (empty($stafDate)) {
                Flash::error(__('messages.not_found', ['model' => __('models/stafdates.singular')]));
                return redirect(route('staf-dates.index'));
            }

            $input = $request->all();
            $this->stafDateRepository->update($input, $id);

            Flash::success(__('messages.updated', ['model' => __('models/stafdates.singular')]));
            return redirect(route('staf-dates.index'));
        } catch (\Exception $e) {
            Log::error('StafDateController@update error: ' . $e->getMessage());
            Flash::error('Error updating staff date: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $stafDate = $this->stafDateRepository->find($id);

            if (empty($stafDate)) {
                Flash::error(__('messages.not_found', ['model' => __('models/stafdates.singular')]));
                return redirect(route('staf-dates.index'));
            }

            $this->stafDateRepository->delete($id);

            Flash::success(__('messages.deleted', ['model' => __('models/stafdates.singular')]));
            return redirect(route('staf-dates.index'));
        } catch (\Exception $e) {
            Log::error('StafDateController@destroy error: ' . $e->getMessage());
            Flash::error('Error deleting staff date: ' . $e->getMessage());
            return redirect(route('staf-dates.index'));
        }
    }
}
