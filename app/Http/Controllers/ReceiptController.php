<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\ReceiptDataTable;
use App\Repositories\ReceiptRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateReceiptRequest;
use App\Http\Requests\UpdateReceiptRequest;

class ReceiptController extends AppBaseController
{
    /** @var  ReceiptRepository */
    private $receiptRepository;

    public function __construct(ReceiptRepository $receiptRepo)
    {
        $this->receiptRepository = $receiptRepo;
    }

    /**
     * Display a listing of the Receipt.
     *
     * @param ReceiptDataTable $receiptDataTable
     * @return Response
     */
    public function index(ReceiptDataTable $receiptDataTable)
    {
        return $receiptDataTable->render('receipts.index');
    }

    /**
     * Show the form for creating a new Receipt.
     *
     * @return Response
     */
    public function create()
    {
        return view('receipts.create');
    }

    /**
     * Store a newly created Receipt in storage.
     *
     * @param CreateReceiptRequest $request
     *
     * @return Response
     */
    public function store(CreateReceiptRequest $request)
    {
        $input = $request->all();

        $receipt = $this->receiptRepository->create($input);

        if($receipt == true)
            Flash::success(__('messages.saved', ['model' => __('models/receipts.singular')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('receipts.index'));
    }

    /**
     * Display the specified Receipt.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $receipt = $this->receiptRepository->find($id);
        if (empty($receipt)) {
            Flash::error(__('models/receipts.singular').' '.__('messages.not_found'));
            return redirect(route('receipts.index'));
        }
        // find also those who have same created time filed
        $invoices = \App\Models\Receipt::with('transactionable:id,invoice_no')->where('type','Receipt')->where('created_at', '=', $receipt->created_at)->get();
        // dd($invoices->toArray());
        return view('receipts.show', [
            'receipt' => $receipt,
            'invoices' => $invoices
        ]);
    }

    /**
     * Show the form for editing the specified Receipt.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $receipt = $this->receiptRepository->find($id);

        if (empty($receipt)) {
            Flash::error(__('messages.not_found', ['model' => __('models/receipts.singular')]));

            return redirect(route('receipts.index'));
        }

        return view('receipts.edit')->with('receipt', $receipt);
    }

    /**
     * Update the specified Receipt in storage.
     *
     * @param  int              $id
     * @param UpdateReceiptRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReceiptRequest $request)
    {
        $receipt = $this->receiptRepository->find($id);

        if (empty($receipt)) {
            Flash::error(__('messages.not_found', ['model' => __('models/receipts.singular')]));

            return redirect(route('receipts.index'));
        }

        $receipt = $this->receiptRepository->update($request->all(), $id);

        if($receipt == true)
            Flash::success(__('messages.updated', ['model' => __('models/receipts.singular')]));
        else
            Flash::error(__('messages.db_error'));

        return redirect(route('receipts.index'));
    }

    /**
     * Remove the specified Receipt from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $receipt = $this->receiptRepository->find($id);

        if (empty($receipt)) {
            Flash::error(__('messages.not_found', ['model' => __('models/receipts.singular')]));

            return redirect(route('receipts.index'));
        }

        $status = $this->receiptRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/receipts.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('receipts.index'));
    }

    // Company invoices
    public function company_invoices(Request $request)
    {
        $invoices = \App\Models\Invoice::with('quotation')
            ->whereHas('quotation',function($query) use ($request) {
                $query->where('company_id', $request->get('company_id'));
            })
            ->where('status',0)->get();

        if($invoices)
            return response()->json($invoices);
        else
            return response()->json(false, 422);
    }
}
