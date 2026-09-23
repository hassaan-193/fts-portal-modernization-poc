<?php

namespace App\Http\Controllers;

use App\DataTables\LpooutDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateLpooutRequest;
use App\Http\Requests\UpdateLpooutRequest;
use App\Repositories\LpooutRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Models\Lpoout;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Services\LpoPdfService;

class LpooutController extends AppBaseController
{
    /** @var  LpooutRepository */
    private $lpooutRepository;

    public function __construct(LpooutRepository $lpooutRepo)
    {
        $this->middleware('can:lpoouts');
        $this->lpooutRepository = $lpooutRepo;
    }

    /**
     * Display a listing of the Lpoout.
     *
     * @param LpooutDataTable $lpooutDataTable
     * @return Response
     */
    public function index(LpooutDataTable $lpooutDataTable)
    {
        return $lpooutDataTable->render('lpoouts.index');
    }

    /**
     * Show the form for creating a new Lpoout.
     *
     * @return Response
     */
    public function create()
    {
        return view('lpoouts.create');
    }

    /**
     * Store a newly created Lpoout in storage.
     *
     * @param CreateLpooutRequest $request
     *
     * @return Response
     */
    public function store(CreateLpooutRequest $request)
    {
        $input = $request->all();

        // Clean up items array - remove empty rows
        if (isset($input['items']) && is_array($input['items'])) {
            $input['items'] = array_values(array_filter($input['items'], function($item) {
                return !empty($item['description']) || !empty($item['total']);
            }));
        }

        $input['lpo_invoice_no'] = $this->generateLpoInvoiceNo($request);

        $lpoout = $this->lpooutRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/lpoouts.singular')]));

        return redirect(route('lpoouts.index'));
    }

    /**
     * Display the specified Lpoout.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (empty($lpoout)) {
            Flash::error(__('models/lpoouts.singular').' '.__('messages.not_found'));

            return redirect(route('lpoouts.index'));
        }
        $lpoout->load('project', 'revisedByUser', 'purchaseOrder', 'paymentInvoices.transaction');

        $allRevisions = \App\Models\Lpoout::where('lpo_invoice_no', $lpoout->lpo_invoice_no)
            ->with('revisedByUser')
            ->orderBy('revision_number')
            ->get();

        $revisionCount = $allRevisions->count();

        return view('lpoouts.show', compact('lpoout', 'allRevisions', 'revisionCount'));
    }

    /**
     * Show the form for editing the specified Lpoout.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (empty($lpoout)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoouts.singular')]));

            return redirect(route('lpoouts.index'));
        }

        return view('lpoouts.edit')->with('lpoout', $lpoout);
    }

    /**
     * Update the specified Lpoout in storage.
     *
     * @param  int              $id
     * @param UpdateLpooutRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLpooutRequest $request)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (empty($lpoout)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoouts.singular')]));

            return redirect(route('lpoouts.index'));
        }

        $input = $request->all();

        // Clean up items array - remove empty rows
        if (isset($input['items']) && is_array($input['items'])) {
            $input['items'] = array_values(array_filter($input['items'], function($item) {
                return !empty($item['description']) || !empty($item['total']);
            }));
        }

        $lpoout = $this->lpooutRepository->update($input, $id);

        Flash::success(__('messages.updated', ['model' => __('models/lpoouts.singular')]));

        return redirect(route('lpoouts.index'));
    }

    /**
     * Remove the specified Lpoout from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (empty($lpoout)) {
            Flash::error(__('messages.not_found', ['model' => __('models/lpoouts.singular')]));

            return redirect(route('lpoouts.index'));
        }

        $status = $this->lpooutRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/lpoouts.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('lpoouts.index'));
    }


        /**
     * Start a revision of an LPO.
     * Revising no longer edits the LPO in place - it restarts the full
     * Purchase Request workflow (Department review -> Admin approval) with
     * the LPO's data pre-filled, via PurchaseOrderController::create().
     * The original LPO is only archived once the new request is Admin Approved
     * and a fresh LPO has been generated (see PurchaseOrderController::createLpoutFromPurchaseOrder()).
     */
    public function showRevise($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (!$lpoout) {
            Flash::error('LPO not found.');
            return redirect()->route('lpoouts.index');
        }

        if (!$lpoout->canBeRevised()) {
            Flash::error('This LPO cannot be revised because a payment has already been initiated against it, or it is not the latest revision.');
            return redirect()->route('lpoouts.show', $id);
        }

        if ($lpoout->hasPendingRevisionRequest()) {
            Flash::error('A revision request for this LPO is already in progress (pending Department or Admin review).');
            return redirect()->route('lpoouts.show', $id);
        }

        return redirect()->route('purchase-orders.create', ['revise_from_lpoout' => $id]);
    }

    public function approve($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (!$lpoout) {
            return redirect()->back()->with('error', 'LPO not found');
        }

        $this->lpooutRepository->update(['status' => 'Approved'], $id);

        return redirect()->route('lpoouts.manageStatus')->with('success', 'LPO Approved successfully');
    }

    public function disapprove($id)
    {
        $lpoout = $this->lpooutRepository->find($id);

        if (!$lpoout) {
            return redirect()->back()->with('error', 'LPO not found');
        }

        $this->lpooutRepository->update(['status' => 'Not Approved'], $id);

        return redirect()->route('lpoouts.manageStatus')->with('success', 'LPO Disapproved successfully');
    }
    
    public function manageStatus(Request $request)
    {
        $query = Lpoout::query();

        // Filter by Vendor
        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by Status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', ucfirst($request->status)); // Pending / Approved / Not Approved
        }

        // Filter by date
        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $lpoouts = $query->get();
        $vendors = \App\Models\Vendor::all();

        return view('lpoouts.status', compact('lpoouts', 'vendors'));
    }
        public function getVat($id)
{
    $vendor = Vendor::find($id);
    return response()->json([
        'vat_no' => $vendor->vat_no ?? ''
    ]);
}

public function getVendorPaymentPreference($id)
{
    $vendor = Vendor::find($id);
    
    if (!$vendor) {
        return response()->json(['error' => 'Vendor not found'], 404);
    }
    
    return response()->json([
        'payment_preference' => $vendor->payment_preference ?? 'cod',
        'pdc_number_of_days' => $vendor->pdc_number_of_days ?? null,
        'pdc_payment_option' => $vendor->pdc_payment_option ?? null,
    ]);
}

public function print(Lpoout $lpoout)
{
    return view('lpoouts.print', compact('lpoout'));
}

public function downloadPdf(Lpoout $lpoout, LpoPdfService $pdfService)
{
    try {
        $mergedPath = $pdfService->generateMergedPdf($lpoout);
        $filename = 'LPO_' . ($lpoout->lpo_invoice_no ?? $lpoout->id) . '_merged.pdf';

        return response()->download($mergedPath, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    } catch (\Exception $e) {
        \Log::error('LPO PDF download failed: ' . $e->getMessage());
        Flash::error('Failed to generate PDF: ' . $e->getMessage());
        return redirect()->back();
    }
}

/**
 * Generate LPO invoice number: {COMPANY3}-LPO-{COUNTER}-{MMYY}
 * Example: TELE-LPO-3470-0726
 *
 * COMPANY3 = first 3 uppercase letters of the company name.
 * COUNTER  = single global sequence, shared across all companies and months
 *            (see Lpoout::nextLpoCounter()).
 */
private function generateLpoInvoiceNo($request): string
{
    $company = null;

    // Get company from project's quotation
    if (!empty($request->project_id)) {
        $project = \App\Models\Project::find($request->project_id);
        if ($project && $project->quotation) {
            $company = $project->quotation->company;
        }
    }

    return Lpoout::buildLpoInvoiceNo(Lpoout::companyPrefix($company));
}

}
