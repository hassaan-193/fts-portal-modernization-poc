<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentInvoice;
use App\Models\Vendor;
use App\Models\Project;
use App\DataTables\VendorPayableDataTable;

class VendorPayableController extends AppBaseController
{
    public function index(VendorPayableDataTable $dataTable)
    {
        return $dataTable->render('vendor_payables.index');
    }

    public function create()
    {
        $vendors      = Vendor::orderBy('name')->pluck('name', 'id');
        $projects     = Project::orderBy('id')->pluck('subject', 'id');

        return view('vendor_payables.create', compact('vendors', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'        => 'required|exists:vendors,id',
            'project_id'       => 'nullable|exists:projects,id',
            'amount'           => 'required|numeric|min:0.01',
            'due_date'         => 'nullable|date',
            'reference_no'     => 'nullable|string|max:255',
            'source_reference' => 'nullable|string|max:500',
            'note'             => 'nullable|string|max:2000',
            'document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $vendor  = Vendor::find($request->vendor_id);
        $project = $request->project_id ? Project::find($request->project_id) : null;
        $amount  = (float) $request->amount;

        $invoiceNo = $request->reference_no ?: ('VP-' . date('Ymd') . '-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $vendor->name), 0, 4)) . '-' . rand(100, 999));

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('vendor_payable_documents', 'public');
        }

        PaymentInvoice::create([
            'type'               => 'VendorPayable',
            'invoice_no'         => $invoiceNo,
            'vendor_id'          => $request->vendor_id,
            'project_id'         => $request->project_id,
            'amount'             => $amount,
            'vat'                => 0,
            'total_amount'       => $amount,
            'note'               => $request->note,
            'status'             => 0,
            'is_historical'      => true,
            'source_reference'   => $request->source_reference,
            'source_date'        => $request->due_date,
            'historical_party'   => $vendor->name,
            'historical_project' => $project ? $project->subject : null,
            'created_by'         => Auth::id(),
            'document_path'      => $documentPath,
        ]);

        Flash::success('Vendor payable created. It will appear in the payment queue under this vendor.');
        return redirect()->route('vendor-payables.index');
    }

    public function show($id)
    {
        $payable = PaymentInvoice::with(['vendor', 'project', 'transaction.transaction_payment_type'])
            ->where('is_historical', true)
            ->findOrFail($id);

        return view('vendor_payables.show', compact('payable'));
    }

    public function edit($id)
    {
        $payable = PaymentInvoice::where('is_historical', true)->findOrFail($id);

        if ($payable->status !== 0) {
            Flash::error('This payable cannot be edited — it has already been paid or cancelled.');
            return redirect()->route('vendor-payables.show', $id);
        }

        $vendors  = Vendor::orderBy('name')->pluck('name', 'id');
        $projects = Project::orderBy('id')->pluck('subject', 'id');

        return view('vendor_payables.edit', compact('payable', 'vendors', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $payable = PaymentInvoice::where('is_historical', true)->findOrFail($id);

        if ($payable->status !== 0) {
            Flash::error('Cannot update a paid or cancelled payable.');
            return redirect()->route('vendor-payables.show', $id);
        }

        $request->validate([
            'vendor_id'        => 'required|exists:vendors,id',
            'project_id'       => 'nullable|exists:projects,id',
            'amount'           => 'required|numeric|min:0.01',
            'due_date'         => 'nullable|date',
            'reference_no'     => 'nullable|string|max:255',
            'source_reference' => 'nullable|string|max:500',
            'note'             => 'nullable|string|max:2000',
            'document'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $vendor  = Vendor::find($request->vendor_id);
        $project = $request->project_id ? Project::find($request->project_id) : null;
        $amount  = (float) $request->amount;

        $documentPath = $payable->document_path;
        if ($request->hasFile('document')) {
            if ($payable->document_path) {
                Storage::disk('public')->delete($payable->document_path);
            }
            $documentPath = $request->file('document')->store('vendor_payable_documents', 'public');
        }

        $payable->update([
            'vendor_id'          => $request->vendor_id,
            'project_id'         => $request->project_id,
            'invoice_no'         => $request->reference_no ?: $payable->invoice_no,
            'amount'             => $amount,
            'total_amount'       => $amount,
            'note'               => $request->note,
            'source_reference'   => $request->source_reference,
            'source_date'        => $request->due_date,
            'historical_party'   => $vendor->name,
            'historical_project' => $project ? $project->subject : null,
            'document_path'      => $documentPath,
        ]);

        Flash::success('Vendor payable updated.');
        return redirect()->route('vendor-payables.show', $id);
    }

    public function cancel($id)
    {
        $payable = PaymentInvoice::where('is_historical', true)->findOrFail($id);

        if ($payable->status === 1) {
            Flash::error('Cannot cancel a payable that has already been paid. Delete the payment transaction first.');
            return redirect()->route('vendor-payables.show', $id);
        }

        if ($payable->status === -1) {
            Flash::warning('This payable is already cancelled.');
            return redirect()->route('vendor-payables.show', $id);
        }

        $payable->update(['status' => -1]);

        Flash::success('Payable cancelled and removed from the payment queue.');
        return redirect()->route('vendor-payables.index');
    }

    public function destroy($id)
    {
        $payable = PaymentInvoice::where('is_historical', true)->findOrFail($id);

        if ($payable->status === 1) {
            Flash::error('Cannot delete a paid payable. Delete the payment transaction first if you need to reverse it.');
            return redirect()->route('vendor-payables.show', $id);
        }

        $payable->delete();

        Flash::success('Vendor payable deleted.');
        return redirect()->route('vendor-payables.index');
    }
}
