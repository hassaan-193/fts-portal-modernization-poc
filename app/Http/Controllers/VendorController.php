<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\VendorDataTable;
use App\Repositories\VendorRepository;
use App\Http\Requests\CreateVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Controllers\AppBaseController;

class VendorController extends AppBaseController
{
    /** @var  VendorRepository */
    private $vendorRepository;

    public function __construct(VendorRepository $vendorRepo)
    {
        $this->vendorRepository = $vendorRepo;
    }

    /**
     * Display a listing of the Vendor.
     *
     * @param VendorDataTable $vendorDataTable
     * @return Response
     */
    public function index(VendorDataTable $vendorDataTable)
    {
        return $vendorDataTable->render('vendors.index');
    }

    /**
     * Show the form for creating a new Vendor.
     *
     * @return Response
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created Vendor in storage.
     *
     * @param CreateVendorRequest $request
     *
     * @return Response
     */
    public function store(CreateVendorRequest $request)
    {
        $input = $this->normaliseEmails($request->all());

        $vendor = $this->vendorRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/vendors.singular')]));

        return redirect(route('vendors.index'));
    }

    /**
     * Display the specified Vendor.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $vendor = $this->vendorRepository->find($id);

        if (empty($vendor)) {
            Flash::error(__('models/vendors.singular').' '.__('messages.not_found'));

            return redirect(route('vendors.index'));
        }

        return view('vendors.show')->with('vendor', $vendor);
    }

    /**
     * Show the form for editing the specified Vendor.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $vendor = $this->vendorRepository->find($id);

        if (empty($vendor)) {
            Flash::error(__('messages.not_found', ['model' => __('models/vendors.singular')]));

            return redirect(route('vendors.index'));
        }

        return view('vendors.edit')->with('vendor', $vendor);
    }

    /**
     * Update the specified Vendor in storage.
     *
     * @param  int              $id
     * @param UpdateVendorRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateVendorRequest $request)
    {
        $vendor = $this->vendorRepository->find($id);

        if (empty($vendor)) {
            Flash::error(__('messages.not_found', ['model' => __('models/vendors.singular')]));

            return redirect(route('vendors.index'));
        }

        $vendor = $this->vendorRepository->update($this->normaliseEmails($request->all()), $id);

        Flash::success(__('messages.updated', ['model' => __('models/vendors.singular')]));

        return redirect(route('vendors.index'));
    }

    /**
     * Remove the specified Vendor from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $vendor = $this->vendorRepository->find($id);

        if (empty($vendor)) {
            Flash::error(__('messages.not_found', ['model' => __('models/vendors.singular')]));

            return redirect(route('vendors.index'));
        }

        $status = $this->vendorRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/vendors.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('vendors.index'));
    }

    /**
     * Drop blank rows from the repeatable "Additional Emails" input and skip any
     * that merely repeat the primary address, so the stored list is the extras only.
     */
    private function normaliseEmails(array $input): array
    {
        $primary = trim((string) ($input['email'] ?? ''));

        $emails = is_array($input['emails'] ?? null) ? $input['emails'] : [];
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails, function ($email) use ($primary) {
            return $email !== '' && strcasecmp($email, $primary) !== 0;
        });

        $input['emails'] = array_values(array_unique($emails));

        return $input;
    }

    // Add Vendor by ajax
    public function add_vendor_ajax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'vat_no' => 'required',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->messages()->get('*');
            return response()->json($errors, 422);
        }

        $input = $request->all();
        $vendor = $this->vendorRepository->create($input);

        if($vendor)
            return response()->json($vendor);
        else
            return response()->json('Unable to add vendor!', 423);
    }

    public function getTermsAndConditions($vendorId)
    {
        $vendor = $this->vendorRepository->find($vendorId);

        if (!$vendor) {
            return response()->json(['terms_and_conditions' => '']);
        }

        return response()->json([
            'terms_and_conditions' => $vendor->terms_and_conditions ?? ''
        ]);
    }
}
