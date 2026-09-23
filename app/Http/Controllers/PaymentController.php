<?php

namespace App\Http\Controllers;

use App\DataTables\PaymentDataTable;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Repositories\PaymentRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class PaymentController extends AppBaseController
{
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Payment.
     *
     * @param PaymentDataTable $paymentDataTable
     * @return Response
     */
    public function index(PaymentDataTable $paymentDataTable)
    {
        return $paymentDataTable->render('payments.index');
    }

    /**
     * Show the form for creating a new Payment.
     *
     * @return Response
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created Payment in storage.
     *
     * @param CreatePaymentRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentRequest $request)
    {
        $input = $request->all();

        $payment = $this->paymentRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/payments.singular')]));

        return redirect(route('payments.index'));
    }

    /**
     * Display the specified Payment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            Flash::error(__('models/payments.singular').' '.__('messages.not_found'));

            return redirect(route('payments.index'));
        }

        return view('payments.show')->with('payment', $payment);
    }

    /**
     * Show the form for editing the specified Payment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payments.singular')]));

            return redirect(route('payments.index'));
        }

        return view('payments.edit')->with('payment', $payment);
    }

    /**
     * Update the specified Payment in storage.
     *
     * @param  int              $id
     * @param UpdatePaymentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentRequest $request)
    {
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payments.singular')]));

            return redirect(route('payments.index'));
        }

        $payment = $this->paymentRepository->update($request->all(), $id);

        if($payment == true)
            Flash::success(__('messages.updated', ['model' => __('models/payments.singular')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('payments.index'));
    }

    /**
     * Remove the specified Payment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            Flash::error(__('messages.not_found', ['model' => __('models/payments.singular')]));

            return redirect(route('payments.index'));
        }

        $status = $this->paymentRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/payments.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));


        return redirect(route('payments.index'));
    }

    // Vendor invoices
    public function vendor_invoices(Request $request)
    {
        $invoices = \App\Models\PaymentInvoice::select('id','invoice_no','total_amount')
            ->where(function($query) use ($request) {
                $query->whereHas('lpoout',function($q) use ($request) {
                    $q->where('vendor_id', $request->get('vendor_id'));
                })
                ->orWhere('vendor_id',$request->get('vendor_id'));
            })
            ->where('status',0)->get();

        if($invoices)
            return response()->json($invoices);
        else
            return response()->json(false, 422);
    }
}
