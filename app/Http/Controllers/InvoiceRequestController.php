<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceRequestDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateInvoiceRequestRequest;
use App\Http\Requests\UpdateInvoiceRequestRequest;
use App\Repositories\InvoiceRequestRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class InvoiceRequestController extends AppBaseController
{
    /** @var  InvoiceRequestRepository */
    private $invoiceRequestRepository;

    public function __construct(InvoiceRequestRepository $invoiceRequestRepo)
    {
        $this->invoiceRequestRepository = $invoiceRequestRepo;
    }

    /**
     * Display a listing of the InvoiceRequest.
     *
     * @param InvoiceRequestDataTable $invoiceRequestDataTable
     * @return Response
     */
    public function index(InvoiceRequestDataTable $invoiceRequestDataTable)
    {
        return $invoiceRequestDataTable->render('invoice_requests.index');
    }

    /**
     * Show the form for creating a new InvoiceRequest.
     *
     * @return Response
     */
    public function create()
    {
        return view('invoice_requests.create');
    }

    public function create_with_lpoin($lpoin)
    {
        $lpoin = \App\Models\Lpoin::find($lpoin);
        if(!$lpoin){
            Flash::error(__('models/lpoins.singular').' '.__('messages.not_found'));
            return redirect()->back();
        }
        return view('invoice_requests.create')->with('lpoin' , $lpoin);
    }

    public function create_with_lpoout($lpooutId)
    {
        $lpoout = \App\Models\Lpoout::with(['vendor', 'paymentInvoices'])->find($lpooutId);
        if (!$lpoout) {
            Flash::error('LPO not found.');
            return redirect()->back();
        }
        return view('invoice_requests.create_lpoout', compact('lpoout'));
    }

    public function store_for_lpoout(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'lpoout_id'     => 'required|integer|exists:lpoouts,id',
            'note'          => 'required|string|max:1000',
            'delivery_date' => 'nullable|date',
        ]);

        \App\Models\InvoiceRequest::create([
            'user_id'          => auth()->id(),
            'requestable_type' => \App\Models\Lpoout::class,
            'requestable_id'   => $request->lpoout_id,
            'note'             => $request->note,
            'delivery_date'    => $request->delivery_date,
            'status'           => 0,
        ]);

        Flash::success('Invoice request submitted successfully.');
        return redirect(route('lpoouts.show', $request->lpoout_id));
    }
    /**
     * Store a newly created InvoiceRequest in storage.
     *
     * @param CreateInvoiceRequestRequest $request
     *
     * @return Response
     */
    public function store(CreateInvoiceRequestRequest $request)
    {
        $input = $request->all();

        $invoiceRequest = $this->invoiceRequestRepository->create($input);
        if($invoiceRequest)
            Flash::success(__('messages.saved', ['model' => __('models/invoice_requests.singular')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('invoiceRequests.index'));
    }

    /**
     * Display the specified InvoiceRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $invoiceRequest = $this->invoiceRequestRepository->find($id);

        if (empty($invoiceRequest)) {
            Flash::error(__('models/invoice_requests.singular').' '.__('messages.not_found'));

            return redirect(route('invoiceRequests.index'));
        }

        return view('invoice_requests.show')->with('invoiceRequest', $invoiceRequest);
    }

    /**
     * Show the form for editing the specified InvoiceRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $invoiceRequest = $this->invoiceRequestRepository->find($id);

        if (empty($invoiceRequest)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoice_requests.singular')]));

            return redirect(route('invoiceRequests.index'));
        }
        // dd($invoiceRequest->toArray());
        return view('invoice_requests.edit')->with('invoiceRequest', $invoiceRequest);
    }

    /**
     * Update the specified InvoiceRequest in storage.
     *
     * @param  int              $id
     * @param UpdateInvoiceRequestRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInvoiceRequestRequest $request)
    {
        $request->route()->setParameter('invoiceRequest', $id);

        $invoiceRequest = $this->invoiceRequestRepository->find($id);

        if (empty($invoiceRequest)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoice_requests.singular')]));

            return redirect(route('invoiceRequests.index'));
        }

        $invoiceRequest = $this->invoiceRequestRepository->updateRequest($request->all(), $invoiceRequest);
        if($invoiceRequest)
            Flash::success(__('messages.updated', ['model' => __('models/invoice_requests.singular')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('invoiceRequests.index'));
    }

    /**
     * Remove the specified InvoiceRequest from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $invoiceRequest = $this->invoiceRequestRepository->find($id);

        if (empty($invoiceRequest)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoice_requests.singular')]));

            return redirect(route('invoiceRequests.index'));
        }

        $this->invoiceRequestRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => __('models/invoice_requests.singular')]));

        return redirect(route('invoiceRequests.index'));
    }
    public function get_quotation_invoices($id)
    {
        $quotation = \App\Models\Quotation::with('invoices.invoice_service_details')->find($id);
        foreach ($quotation->invoices as $invoice) {
            $invoice->government_fee = $invoice->invoice_service_details->sum('amount');
            // Check if the invoice is overdue
            $invoice->is_overdue = $invoice->end_date < now()->subDays(30);
        }
        return response()->json(['sum' => $quotation->total_amount,'invoices' => $quotation ? $quotation->invoices : []]);
    }
    public function get_quotation_invoice_requests($id)
    {
        $quotation = \App\Models\Quotation::with('invoice_requests.user')->find($id);
        return response()->json($quotation ? $quotation->invoice_requests : []);
    }
    public function get_quotation_invoice_payments($id)
    {
        $quotation = \App\Models\Quotation::with('receipts.transaction_payment_type')->find($id);
        return response()->json($quotation ? $quotation->receipts : []);
    }
    /**
     * Get quotation details for selected Lpoin (via quotation_id)
     *
     * @param  int $quotationId
     * @return Response
     */
    public function getQuotationDetails($quotationId)
    {
        try {
            $quotation = \App\Models\Quotation::with('invoices')->find($quotationId);
            
            if (!$quotation) {
                return response()->json([
                    'error' => 'Quotation not found'
                ], 404);
            }
            
            // Get all invoices for this quotation
            $allInvoices = $quotation->invoices;
            $totalInvoices = $allInvoices->count();
            
            // Get completed invoices (status = 1)
            $completedInvoices = $allInvoices->where('status', 1);
            $completedCount = $completedInvoices->count();
            
            // Get pending invoices (status != 1, which could be 0 or other statuses)
            $pendingCount = $allInvoices->where('status', '!=', 1)->count();
            
            // Calculate completed invoices total amount
            $completedInvoicesTotal = $completedInvoices->sum('amount'); 
            
            // Calculate remaining amount (CV - completed invoices)
            $remainingAmount = $quotation->amount - $completedInvoicesTotal;
            
            return response()->json([
                'cv_amount' => number_format($quotation->amount, 2),
                'remaining_amount' => number_format($remainingAmount, 2),
                'total_invoices' => $totalInvoices,
                'completed_invoices' => $completedCount,
                'pending_invoices' => $pendingCount,
                'completed_total' => number_format($completedInvoicesTotal, 2)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching quotation details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error fetching quotation details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
