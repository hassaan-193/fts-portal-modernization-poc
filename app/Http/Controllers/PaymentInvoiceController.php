<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use App\DataTables\PaymentInvoiceDataTable;
use App\Http\Controllers\AppBaseController;
use App\Repositories\PaymentInvoiceRepository;
use App\DataTables\InvoiceRequestPaymentDataTable;
use App\Http\Requests\CreatePaymentInvoiceRequest;
use App\Http\Requests\UpdatePaymentInvoiceRequest;

class PaymentInvoiceController extends AppBaseController
{
    /** @var  PaymentInvoiceRepository */
    private $paymentInvoiceRepository;

    public function __construct(PaymentInvoiceRepository $paymentInvoiceRepo)
    {
        $this->paymentInvoiceRepository = $paymentInvoiceRepo;
    }

    /**
     * Display a listing of the PaymentInvoice.
     *
     * @param PaymentInvoiceDataTable $paymentInvoiceDataTable
     * @return Response
     */
    public function index(PaymentInvoiceDataTable $paymentInvoiceDataTable)
    {
        return $paymentInvoiceDataTable->render('payment_invoices.index');
    }

    /**
     * Show the form for creating a new PaymentInvoice.
     *
     * @return Response
     */
    public function create()
    {
        return view('payment_invoices.create');
    }

    public function create_request_invoice($requestId)
    {
        $request = $requestId;
        $invoiceRequest = \App\Models\InvoiceRequest::with(['requestable.vendor', 'requestable.paymentInvoices'])->find($requestId);
        $lpoout = $invoiceRequest && $invoiceRequest->requestable instanceof \App\Models\Lpoout
            ? $invoiceRequest->requestable
            : null;
        return view('payment_invoices.create', compact('request', 'invoiceRequest', 'lpoout'));
    }

    public function createFromLpoout($lpooutId)
    {
        $lpoout = \App\Models\Lpoout::with('vendor')->find($lpooutId);

        if (!$lpoout) {
            Flash::error('LPO not found.');
            return redirect()->back();
        }

        return view('payment_invoices.create', compact('lpoout'));
    }

    public function storeFromLpoout(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'lpoout_id' => 'required|integer|exists:lpoouts,id',
            'amount'    => 'required|numeric|min:0.01',
        ]);

        $lpoout = \App\Models\Lpoout::find($request->lpoout_id);

        $input = $request->only([
            'lpoout_id', 'invoice_no', 'start_date', 'end_date', 'amount', 'vat', 'note',
        ]);
        $input['type']       = 'LpoOut';
        $input['status']     = 0;
        $input['vendor_id']  = $lpoout->vendor_id;
        $input['project_id'] = $lpoout->project_id;

        $paymentInvoice = $this->paymentInvoiceRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/payment_invoices.singular')]));

        return redirect(route('lpoouts.show', $request->lpoout_id));
    }

    /**
     * Store a newly created PaymentInvoice in storage.
     *
     * @param CreatePaymentInvoiceRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentInvoiceRequest $request)
    {
        $input = $request->all();

        $paymentInvoice = $this->paymentInvoiceRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/payment_invoices.singular')]));

        return redirect(route('paymentInvoices.index'));
    }

    /**
     * Display the specified PaymentInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $paymentInvoice = $this->paymentInvoiceRepository->find($id);

        if (empty($paymentInvoice)) {
            Flash::error(__('models/payment_invoices.singular').' '.__('messages.not_found'));

            return redirect(route('paymentInvoices.index'));
        }

        return view('payment_invoices.show')->with('paymentInvoice', $paymentInvoice);
    }

    /**
     * Show the form for editing the specified PaymentInvoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $paymentInvoice = $this->paymentInvoiceRepository->find($id);

        if (empty($paymentInvoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payment_invoices.singular')]));

            return redirect(route('paymentInvoices.index'));
        }

        return view('payment_invoices.edit')->with('paymentInvoice', $paymentInvoice);
    }

    /**
     * Update the specified PaymentInvoice in storage.
     *
     * @param  int              $id
     * @param UpdatePaymentInvoiceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentInvoiceRequest $request)
    {
        $paymentInvoice = $this->paymentInvoiceRepository->find($id);

        if (empty($paymentInvoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payment_invoices.singular')]));

            return redirect(route('paymentInvoices.index'));
        }

        $paymentInvoice = $this->paymentInvoiceRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/payment_invoices.singular')]));

        return redirect(route('paymentInvoices.index'));
    }

    /**
     * Remove the specified PaymentInvoice from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $paymentInvoice = $this->paymentInvoiceRepository->find($id);

        if (empty($paymentInvoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payment_invoices.singular')]));

            return redirect(route('paymentInvoices.index'));
        }

        $status = $this->paymentInvoiceRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/payment_invoices.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('paymentInvoices.index'));
    }

    /**
     * Display a listing of the InvoiceRequest.
     *
     * @param InvoiceRequestDataTable $invoiceRequestDataTable
     * @return Response
     */
    public function request_invoices(InvoiceRequestPaymentDataTable $invoiceRequestPaymentDataTable)
    {
        return $invoiceRequestPaymentDataTable->render('invoice_requests.index');
    }
}
