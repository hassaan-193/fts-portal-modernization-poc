<?php

namespace App\Http\Controllers;

use App\DataTables\PurchaseOrderDataTable;
use App\DataTables\PurchaseOrderDepartmentDataTable;
use App\DataTables\PurchaseOrderAdminDataTable;
use App\Http\Requests;
use App\Http\Requests\CreatePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Http\Requests\ApprovePoRequest;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\LpooutRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\Lpoin;
use Illuminate\Http\Request;

class PurchaseOrderController extends AppBaseController
{
    private $purchaseOrderRepository;
    private $lpooutRepository;

    public function __construct(PurchaseOrderRepository $poRepo, LpooutRepository $lpoRepo)
    {
        // $this->middleware('can:purchase-orders');
        $this->purchaseOrderRepository = $poRepo;
        $this->lpooutRepository = $lpoRepo;
    }

    public function index(PurchaseOrderDataTable $dataTable)
    {
        return $dataTable->render('purchase-orders.index');
    }

    public function create(Request $request)
    {
        $quotations = $this->getLpoinQuotationOptions();
        $projects = Project::where('category', 'amc')
            ->with('quotation:id,ref_no')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => $p->subject . (optional($p->quotation)->ref_no ? ' (' . $p->quotation->ref_no . ')' : '')]);
        $vendors = \App\Models\Vendor::pluck('name', 'id');
        $requestTypes = [
            'general' => 'General',
            'project' => 'Project',
            'maintenance' => 'Maintenance',
            'store' => 'Store'
        ];

        // Resubmit flow: pre-fill the create form from a rejected PO's data.
        // Only rejected POs are eligible; this never modifies the original record.
        // Revision flow: pre-fill the create form from an approved LPO's data, to
        // restart the approval workflow (see LpooutController::showRevise()).
        // Neither branch modifies the source record - both build a transient,
        // unsaved PurchaseOrder used only to feed the existing $po prefill pattern.
        $po = null;
        $revisionSourceLpoout = null;
        if ($request->filled('resubmit_from')) {
            $source = $this->purchaseOrderRepository->find($request->query('resubmit_from'));
            if ($source && $source->status === 'Rejected') {
                $po = $source;
            }
        } elseif ($request->filled('revise_from_lpoout')) {
            $lpoout = \App\Models\Lpoout::find($request->query('revise_from_lpoout'));
            if ($lpoout && $lpoout->canBeRevised() && !$lpoout->hasPendingRevisionRequest()) {
                $po = $this->buildPoPrefillFromLpoout($lpoout);
                $revisionSourceLpoout = $lpoout;
            }
        }

        return view('purchase-orders.create', compact('quotations', 'projects', 'vendors', 'requestTypes', 'po', 'revisionSourceLpoout'));
    }

    public function store(CreatePurchaseOrderRequest $request)
    {
        $input = $request->all();

        if (isset($input['items']) && is_array($input['items'])) {
            $input['items'] = array_values(array_filter($input['items'], function($item) {
                return !empty($item['material_name']) || !empty($item['quantity']);
            }));
        }

        $input['has_item_code'] = (bool) ($input['has_item_code'] ?? false);

        // Generate request number if not provided
        if (empty($input['request_number'])) {
            $input['request_number'] = $this->generateRequestNumber();
        }

        // Remove files from input to prevent them being stored in database
        unset($input['files']);

        // Only keep the revision link when it actually carries an id (avoid
        // persisting 0/'' from the hidden field on ordinary, non-revision POs)
        if (empty($input['revised_from_lpoout_id'])) {
            unset($input['revised_from_lpoout_id']);
        }

        // Store LPOUT form data with PO (Step 1)
        // Note: VAT is handled in Step 2 by department
        $lpout_data = [
            'lpout_name' => $input['name'] ?? null,
            'lpout_vendor_id' => $input['vendor_id'] ?? null,
            // Always our own TRN - the vendor's TRN is never printed or mailed out
            'lpout_trn_no' => config('purchase-orders.company_trn'),
            'lpout_kindly_attn' => $input['kindly_attn'] ?? null,
            'lpout_date' => $input['lpout_date'] ?? null,
            'lpout_payment_type' => $input['payment_type'] ?? null,
            'lpout_cheque_date' => $input['cheque_date'] ?? null,
            'lpout_payment_preference_option' => $input['lpo_payment_preference_option'] ?? 'default',
            'payment_preference' => $input['custom_payment_preference'] ?? null,
            'lpout_pricing_mode' => ($input['pricing_mode'] ?? 'unit') === 'lump' ? 'lump' : 'unit',
            'lpout_manual_total' => (($input['pricing_mode'] ?? 'unit') === 'lump' && ($input['manual_total'] ?? '') !== '')
                ? floatval($input['manual_total'])
                : null,
        ];

        $input = array_merge($input, $lpout_data);
        
        // Ensure VAT is not set from Step 1 (will be decided in Step 2)
        $input['lpout_vat'] = 0;

        $po = $this->purchaseOrderRepository->create($input);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    try {
                        // Store the file in temp-uploads first
                        $storedPath = $file->store('temp-uploads');
                        $fullPath = storage_path('app/' . $storedPath);

                        if (file_exists($fullPath)) {
                            $originalName = $file->getClientOriginalName();
                            $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                            // Add to media collection
                            $po->addMedia($fullPath)
                                ->usingName($originalNameWithoutExt)
                                ->usingFileName($originalName)
                                ->toMediaCollection();

                            // Clean up temp file
                            unlink($fullPath);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Purchase Order file upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        // Send notification email to department reviewers
        try {
            $departmentEmails = config('purchase-orders.department_reviewer_emails', []);
            if (!empty($departmentEmails)) {
                $departmentEmails = is_array($departmentEmails) ? $departmentEmails : [$departmentEmails];
                foreach ($departmentEmails as $email) {
                    if (!empty($email)) {
                        \Mail::to($email)->send(new \App\Mail\PurchaseOrderCreatedMail($po));
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send purchase order notification email: ' . $e->getMessage());
            // Don't fail the main operation if email fails
        }

        // Send WhatsApp notification to configured staff when a PO request is created
        try {
            $whatsAppService = new \App\Services\WhatsAppService();
            $po->load('vendor', 'quotation.company', 'project.quotation.company');
            $result = $whatsAppService->sendPORequestCreatedNotification($po);

            if (!$result['success']) {
                \Log::warning('WhatsApp notification partially failed for PO request', [
                    'po_id' => $po->id,
                    'sent' => $result['sent'] ?? 0,
                    'failed' => $result['failed'] ?? 0,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp PO request notification error for PO ' . $po->id . ': ' . $e->getMessage());
            // Don't fail the main operation if WhatsApp fails
        }

        Flash::success(__('messages.saved', ['model' => 'Purchase Order']));

        return redirect(route('purchase-orders.index'));
    }

    public function show($id)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect(route('purchase-orders.index'));
        }

        return view('purchase-orders.show')->with('po', $po);
    }

    public function edit($id)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect(route('purchase-orders.index'));
        }

        $quotations = $this->getLpoinQuotationOptions();
        $projects = Project::where('category', 'amc')
            ->with('quotation:id,ref_no')
            ->get()
            ->mapWithKeys(fn($p) => [$p->id => $p->subject . (optional($p->quotation)->ref_no ? ' (' . $p->quotation->ref_no . ')' : '')]);
        $requestTypes = [
            'general' => 'General',
            'project' => 'Project',
            'maintenance' => 'Maintenance',
            'store' => 'Store'
        ];

        return view('purchase-orders.edit', compact('po', 'quotations', 'projects', 'requestTypes'));
    }

    public function update($id, UpdatePurchaseOrderRequest $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect(route('purchase-orders.index'));
        }

        $input = $request->all();

        if (isset($input['items']) && is_array($input['items'])) {
            $input['items'] = array_values(array_filter($input['items'], function($item) {
                return !empty($item['material_name']) || !empty($item['quantity']);
            }));
        }

        $input['has_item_code'] = (bool) ($input['has_item_code'] ?? false);

        // A request the department sent back re-enters the workflow from the top:
        // clear the send-back state so it shows up as pending review again.
        $resubmitting = $po->isSentBack();
        if ($resubmitting) {
            $input['status'] = 'Pending';
            $input['department_status'] = 'Pending';
        }

        // Remove files from input to prevent them being stored in database
        unset($input['files']);

        // Map the LPOUT form fields to their actual column names, same as store().
        // Without this, fields below "Local Purchase Outbound (LPOUT) Information"
        // (vendor, payment type, pricing mode, manual total, etc.) are silently
        // dropped by mass assignment since their form names don't match any column.
        $lpout_data = [
            'lpout_name' => $input['name'] ?? $po->lpout_name,
            'lpout_vendor_id' => $input['vendor_id'] ?? null,
            // Always our own TRN - the vendor's TRN is never printed or mailed out
            'lpout_trn_no' => config('purchase-orders.company_trn'),
            'lpout_kindly_attn' => $input['kindly_attn'] ?? null,
            'lpout_date' => $input['lpout_date'] ?? null,
            'lpout_payment_type' => $input['payment_type'] ?? null,
            'lpout_cheque_date' => $input['cheque_date'] ?? null,
            'lpout_payment_preference_option' => $input['lpo_payment_preference_option'] ?? 'default',
            'payment_preference' => $input['custom_payment_preference'] ?? null,
            'lpout_pricing_mode' => ($input['pricing_mode'] ?? 'unit') === 'lump' ? 'lump' : 'unit',
            'lpout_manual_total' => (($input['pricing_mode'] ?? 'unit') === 'lump' && ($input['manual_total'] ?? '') !== '')
                ? floatval($input['manual_total'])
                : null,
        ];

        $input = array_merge($input, $lpout_data);

        $this->purchaseOrderRepository->update($input, $id);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    try {
                        // Store the file in temp-uploads first
                        $storedPath = $file->store('temp-uploads');
                        $fullPath = storage_path('app/' . $storedPath);

                        if (file_exists($fullPath)) {
                            $originalName = $file->getClientOriginalName();
                            $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                            // Add to media collection
                            $po->addMedia($fullPath)
                                ->usingName($originalNameWithoutExt)
                                ->usingFileName($originalName)
                                ->toMediaCollection();

                            // Clean up temp file
                            unlink($fullPath);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Purchase Order file upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        // A corrected request goes back into the department queue, so give the
        // reviewers the same heads-up they get when a request is first raised.
        if ($resubmitting) {
            try {
                $po->refresh();
                $departmentEmails = config('purchase-orders.department_reviewer_emails', []);
                $departmentEmails = is_array($departmentEmails) ? $departmentEmails : [$departmentEmails];

                foreach (array_filter($departmentEmails) as $email) {
                    \Mail::to($email)->send(new \App\Mail\PurchaseOrderCreatedMail($po));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send purchase order resubmission email: ' . $e->getMessage());
                // Don't fail the main operation if email fails
            }

            Flash::success('Purchase Order corrected and resubmitted for department review');

            return redirect(route('purchase-orders.index'));
        }

        Flash::success(__('messages.updated', ['model' => 'Purchase Order']));

        return redirect(route('purchase-orders.index'));
    }

    public function destroy($id)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect(route('purchase-orders.index'));
        }

        $this->purchaseOrderRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => 'Purchase Order']));

        return redirect(route('purchase-orders.index'));
    }

    public function departmentIndex(PurchaseOrderDepartmentDataTable $dataTable)
    {
        return $dataTable->render('purchase-orders.department.index');
    }

    public function departmentEdit($id)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        $vendorItems = \App\Models\Vendor::pluck('name', 'id');
        $lpo_out_typeItems = \App\Models\LpoOutType::pluck('name', 'id');
        $projectItems = \App\Models\Project::pluck('subject', 'id');

        return view('purchase-orders.department.edit', compact('po', 'vendorItems', 'lpo_out_typeItems', 'projectItems'));
    }

    public function departmentUpdate($id, ApprovePoRequest $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }
        
        // Check if PO was already forwarded
        if ($po->department_status === 'Approved' && $po->status === 'Pending Admin Approval') {
            Flash::warning('This Purchase Order has already been forwarded to admin');
            return redirect()->back();
        }

        $isForwarding = $request->input('action', 'forward') === 'forward';

        $input = $request->all();
        if ($isForwarding) {
            $input['status'] = 'Pending Admin Approval';
            $input['department_status'] = 'Approved';
        }

        // PRESERVE the original items from Step 1
        // The form submits cost data to 'lpout_items' instead
        $input['items'] = $po->items; // Keep original items
        
        // LPOUT data was already saved in Step 1, just preserve it
        // Only update lpout_items with cost breakdown from Step 2
        // and VAT flag decided by department
        $input['lpout_items'] = $input['lpout_items'] ?? [];
        $input['lpout_terms'] = $input['terms'] ?? $po->lpout_terms;
        $input['lpout_vat'] = $input['vat'] ?? 0;
        $input['urgency_level'] = $input['urgency_level'] ?? 'normal';

        // The department can turn the item-code column on or off during review
        $input['has_item_code'] = (bool) ($input['has_item_code'] ?? false);

        // Pricing mode: 'lump' = single batch total typed by user, 'unit' = per-unit (sum of rows)
        $input['lpout_pricing_mode'] = ($input['pricing_mode'] ?? 'unit') === 'lump' ? 'lump' : 'unit';
        $input['lpout_manual_total'] = ($input['lpout_pricing_mode'] === 'lump' && ($input['manual_total'] ?? '') !== '')
            ? floatval($input['manual_total'])
            : null;

        unset($input['files']);

        // Update PO with costs and LPOUT form data
        $this->purchaseOrderRepository->update($input, $id);

        // Handle file uploads from department (same pattern as Step 1)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    try {
                        $storedPath = $file->store('temp-uploads');
                        $fullPath = storage_path('app/' . $storedPath);

                        if (file_exists($fullPath)) {
                            $originalName = $file->getClientOriginalName();
                            $originalNameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

                            $po->addMedia($fullPath)
                                ->usingName($originalNameWithoutExt)
                                ->usingFileName($originalName)
                                ->toMediaCollection();

                            unlink($fullPath);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Department file upload failed: ' . $e->getMessage());
                    }
                }
            }
        }

        if (!$isForwarding) {
            Flash::success('Purchase Order changes saved');

            return redirect(route('purchase-orders.departmentEdit', $id));
        }

        // Send WhatsApp notification to configured staff
        try {
            $whatsAppService = new \App\Services\WhatsAppService();
            $po->refresh(); // Refresh to get updated data
            $urgencyLevel = $input['urgency_level'] ?? 'normal';
            $result = $whatsAppService->sendPOForwardedNotification($po, $urgencyLevel);

            if (!$result['success']) {
                \Log::warning('WhatsApp notification partially failed for PO', [
                    'po_id' => $po->id,
                    'sent' => $result['sent'],
                    'failed' => $result['failed'],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp notification error for PO ' . $po->id . ': ' . $e->getMessage());
            // Don't fail the main operation if WhatsApp fails
        }

        Flash::success('Purchase Order approved and forwarded to admin');

        return redirect(route('purchase-orders.departmentIndex'));
    }

    /**
     * Step 2 -> Step 1: hand the request back to whoever raised it instead of
     * forwarding it to admin. The record keeps all its data so the requester can
     * correct it in place and resubmit; PurchaseOrderController::update() clears
     * the send-back state when they do.
     */
    public function departmentSendBack($id, Request $request)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        if ($po->department_status === 'Approved' && $po->status === 'Pending Admin Approval') {
            Flash::warning('This Purchase Order has already been forwarded to admin and can no longer be sent back');
            return redirect()->back();
        }

        $this->purchaseOrderRepository->update([
            'status' => 'Sent Back',
            'department_status' => 'Sent Back',
            'department_notes' => $request->notes,
            'sent_back_notes' => $request->notes,
            'sent_back_at' => now(),
            'sent_back_by' => auth()->id(),
            'sent_back_count' => (int) $po->sent_back_count + 1,
        ], $id);

        // Let the requester know it is waiting on them again
        try {
            $po->refresh();
            $requesterEmail = optional($po->createdBy)->email;

            if (!empty($requesterEmail)) {
                \Mail::to($requesterEmail)->send(new \App\Mail\PurchaseOrderSentBackMail($po, $request->notes));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send purchase order send-back email for PO ' . $id . ': ' . $e->getMessage());
            // Don't fail the main operation if email fails
        }

        Flash::success('Purchase Order sent back to the requester for correction');

        return redirect(route('purchase-orders.departmentIndex'));
    }

    public function departmentReject($id, Request $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        $this->purchaseOrderRepository->update([
            'department_status' => 'Rejected',
            'department_notes' => $request->notes
        ], $id);

        Flash::success('Purchase Order rejected');

        return redirect(route('purchase-orders.departmentIndex'));
    }

    public function adminIndex(PurchaseOrderAdminDataTable $dataTable)
    {
        return $dataTable->render('purchase-orders.admin.index');
    }

    public function adminShow($id)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        // Eager load vendor relationship for LPOUT display
        $po->load('vendor', 'quotation.company', 'project.quotation.company', 'lpout.paymentInvoices.transaction');

        return view('purchase-orders.admin.show', compact('po'));
    }

    public function adminApprove($id, Request $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        // Create LPOUT from stored data if LPOUT hasn't been created yet
        if (empty($po->lpout_id) && !empty($po->lpout_vendor_id)) {
            try {
                // Load relationships needed for company prefix derivation
                $po->load('quotation.company', 'project.quotation.company');
                $lpoout = $this->createLpoutFromPurchaseOrder($po);

                // Update PO with LPOUT link
                $this->purchaseOrderRepository->update(['lpout_id' => $lpoout->id], $id);

                // Send email to vendor with admin approval emails in CC
                try {
                    $vendorEmails = $po->vendor ? $po->vendor->allEmails() : [];

                    if (!empty($vendorEmails)) {
                        $mail = \Mail::mailer('lpouts')->to($vendorEmails);

                        // Add admin approval emails to CC
                        $adminEmails = config('purchase-orders.admin_approval_notification_emails', []);
                        if (!empty($adminEmails)) {
                            $adminEmails = is_array($adminEmails) ? $adminEmails : [$adminEmails];
                            $ccEmails = array_filter($adminEmails);
                            if (!empty($ccEmails)) {
                                $mail->cc($ccEmails);
                            }
                        }

                        $pdfService = new \App\Services\LpoPdfService();
                        $pdfPath = null;
                        try {
                            $pdfPath = $pdfService->generateLpoPdf($lpoout);
                        } catch (\Exception $e) {
                            \Log::error('LPO PDF generation failed: ' . $e->getMessage());
                        }

                        $mail->send(new \App\Mail\LpoutCreatedMail($lpoout, $pdfPath));

                        if ($pdfPath) {
                            $pdfService->cleanup($pdfPath);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send LPOUT email for LPOUT ' . $lpoout->id . ': ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                Flash::error('Error creating LPOUT: ' . $e->getMessage());
                return redirect()->back();
            }
        }

        $this->purchaseOrderRepository->update([
            'status' => 'Admin Approved',
            'admin_id' => auth()->id(),
            'admin_notes' => $request->notes ?? null
        ], $id);

        // Send WhatsApp notification for admin approval
        try {
            $whatsAppService = new \App\Services\WhatsAppService();
            $po->refresh();
            $result = $whatsAppService->sendPOAdminApprovalNotification($po);

            if (!$result['success']) {
                \Log::warning('WhatsApp admin approval notification partially failed for PO', [
                    'po_id' => $po->id,
                    'sent' => $result['sent'],
                    'failed' => $result['failed'],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp admin approval notification error for PO ' . $po->id . ': ' . $e->getMessage());
        }

        Flash::success('Purchase Order approved by admin and LPOUT created');

        return redirect(route('purchase-orders.adminIndex'));
    }

    public function adminApproveWithEmail($id, Request $request)
    {
        // Log incoming request for debugging
        \Log::info('Approval with email - Request received', [
            'po_id' => $id,
            'content_type' => $request->header('Content-Type'),
            'has_cc_emails_input' => $request->has('cc_emails'),
            'has_cc_emails_json' => $request->json('cc_emails') !== null,
            'cc_emails_raw' => $request->input('cc_emails'),
            'cc_emails_json' => $request->json('cc_emails'),
            'all_inputs' => $request->all()
        ]);

        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        // Validate vendor email exists
        $vendorEmails = $po->vendor ? $po->vendor->allEmails() : [];

        if (empty($vendorEmails)) {
            Flash::error('Vendor email is not configured. Please update the vendor profile.');
            return redirect()->back();
        }

        // The modal lets the admin untick individual vendor addresses. Only
        // addresses that actually belong to this vendor are honoured; anything
        // else falls back to the full list on the profile.
        $requestedTo = $request->json('to_emails');
        if ($requestedTo === null) {
            $requestedTo = $request->input('to_emails');
            if (is_string($requestedTo)) {
                $requestedTo = json_decode($requestedTo, true);
            }
        }

        if (is_array($requestedTo)) {
            $selectedTo = array_values(array_filter($vendorEmails, function ($email) use ($requestedTo) {
                foreach ($requestedTo as $candidate) {
                    if (is_string($candidate) && strcasecmp(trim($candidate), $email) === 0) {
                        return true;
                    }
                }
                return false;
            }));

            if (!empty($selectedTo)) {
                $vendorEmails = $selectedTo;
            }
        }

        \Log::info('LPOUT recipients resolved', [
            'po_id' => $id,
            'to_emails' => $vendorEmails,
        ]);

        // Parse CC emails from request (handle both form data and JSON)
        $ccEmails = [];
        
        // First try JSON input (from fetch)
        $ccEmailsFromJson = $request->json('cc_emails');
        if ($ccEmailsFromJson !== null) {
            $ccEmails = is_array($ccEmailsFromJson) ? $ccEmailsFromJson : [];
            \Log::info('CC emails from JSON', ['count' => count($ccEmails), 'emails' => $ccEmails]);
        } 
        // Then try form input (from form submission)
        elseif ($request->has('cc_emails')) {
            try {
                $rawCCEmails = $request->input('cc_emails');
                
                \Log::info('Raw CC emails data from form', [
                    'type' => gettype($rawCCEmails),
                    'value' => $rawCCEmails
                ]);
                
                // Handle both JSON string and direct array
                if (is_string($rawCCEmails)) {
                    $ccEmails = json_decode($rawCCEmails, true) ?? [];
                } else {
                    $ccEmails = is_array($rawCCEmails) ? $rawCCEmails : [];
                }
                
                \Log::info('Parsed CC emails from form before filtering', [
                    'count' => count($ccEmails),
                    'emails' => $ccEmails
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to parse CC emails from form: ' . $e->getMessage());
                $ccEmails = [];
            }
        }
        
        // Filter out invalid emails and ensure they're properly indexed
        $ccEmails = array_values(array_filter($ccEmails, function($email) {
            return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
        
        \Log::info('Final CC emails after filtering', [
            'count' => count($ccEmails),
            'emails' => $ccEmails
        ]);

        // Create LPOUT from stored data if LPOUT hasn't been created yet
        if (empty($po->lpout_id) && !empty($po->lpout_vendor_id)) {
            try {
                $lpoout = $this->createLpoutFromPurchaseOrder($po);
                
                // Update PO with LPOUT link
                $this->purchaseOrderRepository->update(['lpout_id' => $lpoout->id], $id);

                // Send single email to vendor with all CC recipients
                try {
                    $pdfService = new \App\Services\LpoPdfService();
                    $pdfPath = null;
                    try {
                        $pdfPath = $pdfService->generateLpoPdf($lpoout);
                    } catch (\Exception $pdfException) {
                        \Log::error('LPO PDF generation failed: ' . $pdfException->getMessage());
                    }

                    $mailable = new \App\Mail\LpoutCreatedMail($lpoout, $pdfPath);

                    $mailBuilder = \Mail::mailer('lpouts')->to($vendorEmails);

                    if (!empty($ccEmails)) {
                        $mailBuilder = $mailBuilder->cc($ccEmails);
                    }

                    $mailBuilder->send($mailable);

                    if ($pdfPath) {
                        $pdfService->cleanup($pdfPath);
                    }

                    \Log::info('LPOUT email sent successfully', [
                        'lpoout_id' => $lpoout->id,
                        'vendor_emails' => $vendorEmails,
                        'cc_count' => count($ccEmails),
                        'cc_emails' => $ccEmails
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send LPOUT email for LPOUT ' . $lpoout->id . ': ' . $e->getMessage(), [
                        'vendor_emails' => $vendorEmails,
                        'cc_emails' => $ccEmails
                    ]);
                }
            } catch (\Exception $e) {
                Flash::error('Error creating LPOUT: ' . $e->getMessage());
                return redirect()->back();
            }
        }

        $this->purchaseOrderRepository->update([
            'status' => 'Admin Approved',
            'admin_id' => auth()->id(),
            'admin_notes' => $request->notes ?? null
        ], $id);

        Flash::success('Purchase Order approved by admin and LPOUT created. Email sent to vendor and CC recipients.');

        // If this is a JSON request, return JSON response instead of redirect
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order approved successfully',
                'redirect' => route('purchase-orders.adminIndex')
            ]);
        }

        return redirect(route('purchase-orders.adminIndex'));
    }

    public function adminReject($id, Request $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        $this->purchaseOrderRepository->update([
            'status' => 'Rejected',
            'admin_id' => auth()->id(),
            'admin_notes' => $request->notes
        ], $id);

        // Send WhatsApp notification for admin rejection
        try {
            $whatsAppService = new \App\Services\WhatsAppService();
            $po->refresh();
            $rejectionReason = $request->notes ?? '';
            $result = $whatsAppService->sendPOAdminRejectionNotification($po, $rejectionReason);

            if (!$result['success']) {
                \Log::warning('WhatsApp admin rejection notification partially failed for PO', [
                    'po_id' => $po->id,
                    'sent' => $result['sent'],
                    'failed' => $result['failed'],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp admin rejection notification error for PO ' . $po->id . ': ' . $e->getMessage());
        }

        Flash::success('Purchase Order rejected');

        return redirect(route('purchase-orders.adminIndex'));
    }

    public function adminHold($id, Request $request)
    {
        $po = $this->purchaseOrderRepository->find($id);

        if (empty($po)) {
            Flash::error('Purchase Order not found');
            return redirect()->back();
        }

        $this->purchaseOrderRepository->update([
            'status' => 'Hold',
            'admin_id' => auth()->id(),
            'admin_notes' => $request->notes ?? null
        ], $id);

        Flash::success('Purchase Order placed on hold');

        return redirect(route('purchase-orders.adminIndex'));
    }

    /**
     * Options for the "Quotation" select shown when Request Type = Project.
     * Labelled by the client's Lpoin (LPO In) reference instead of the raw
     * quotation name, so staff pick the actual client LPO on file rather than
     * a bare quotation - the value submitted is still the quotation_id
     * (same pattern already used by projects.fields / petty_cashes.fields).
     */
    private function getLpoinQuotationOptions()
    {
        return Lpoin::with('quotation')->get()
            ->filter(fn($lpoin) => $lpoin->quotation)
            ->mapWithKeys(fn($lpoin) => [
                $lpoin->quotation_id => 'LPO In Ref #: ' . $lpoin->ref_no . ' (Quotation: ' . $lpoin->quotation->name . ')',
            ]);
    }

    /**
     * Build an unsaved PurchaseOrder pre-filled from an approved LPO's data, for
     * the "Revise LPO" flow. This never persists anything - it only feeds the
     * existing isset($po) ? $po->field : ... prefill pattern already used by
     * purchase-orders/fields.blade.php for the rejected-PO resubmit flow.
     */
    private function buildPoPrefillFromLpoout(\App\Models\Lpoout $lpoout): PurchaseOrder
    {
        $requestType = 'general';
        if ($lpoout->lpo_out_type) {
            if ($lpoout->lpo_out_type->name === 'Project') {
                $requestType = 'project';
            } elseif ($lpoout->lpo_out_type->name === 'Maintenance') {
                $requestType = 'maintenance';
            }
        }

        $items = collect($lpoout->items ?? [])->map(function ($item) {
            return [
                'item_code' => $item['item_code'] ?? '',
                'material_name' => $item['description'] ?? '',
                'unit' => $item['unit'] ?? '',
                'quantity' => $item['qty'] ?? 0,
                'cost' => $item['unit_price'] ?? 0,
                'total' => $item['total'] ?? 0,
            ];
        })->values()->all();

        // Older LPOs predate the stored pricing mode; fall back to a best-effort
        // guess from the shape of the data. The field is visible/editable in
        // Step 1, so the user can correct it if this guess is wrong.
        $isLumpSum = $lpoout->isLumpSum() || (empty($items) && (float) $lpoout->amount > 0);

        return new PurchaseOrder([
            'request_type' => $requestType,
            'project_id' => $lpoout->project_id,
            'date' => now()->toDateString(),
            'other_info' => "Revision of LPO {$lpoout->lpo_invoice_no} (previous revision #{$lpoout->revision_number}).",
            'items' => $items,
            'lpout_vendor_id' => $lpoout->vendor_id,
            'lpout_trn_no' => config('purchase-orders.company_trn'),
            'lpout_kindly_attn' => $lpoout->kindly_attn,
            'lpout_date' => $lpoout->date,
            'lpout_payment_type' => $lpoout->payment_type,
            'lpout_cheque_date' => $lpoout->cheque_date,
            'lpout_payment_preference_option' => $lpoout->lpo_payment_preference_option,
            'payment_preference' => $lpoout->lpo_payment_preference,
            'lpout_terms' => $lpoout->terms,
            'lpout_pricing_mode' => $isLumpSum ? 'lump' : 'unit',
            'lpout_manual_total' => $isLumpSum ? $lpoout->amount : null,
            'has_item_code' => $lpoout->hasItemCode(),
            'revised_from_lpoout_id' => $lpoout->id,
        ]);
    }

    private function generateRequestNumber()
    {
        $year = date('Y');
        $month = date('m');
        $latestPo = PurchaseOrder::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($latestPo) {
            $lastNumber = $latestPo->request_number;
            preg_match('/(\d+)$/', $lastNumber, $matches);
            if (isset($matches[1])) {
                $sequence = (int)$matches[1] + 1;
            }
        }

        return 'REQ-' . $year . $month . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function createLpoutFromPurchaseOrder($po)
    {
        // Revision flow: if this PO was created from "Revise LPO", the new LPO
        // continues the same revision chain as the original instead of starting a new one.
        $originalLpoout = !empty($po->revised_from_lpoout_id)
            ? \App\Models\Lpoout::find($po->revised_from_lpoout_id)
            : null;

        // Use stored LPOUT data from PO
        $lpoout_items = $po->lpout_items ?? [];
        $isLumpSum = ($po->lpout_pricing_mode ?? 'unit') === 'lump';

        // If no LPOUT items stored, create from PO items
        if (empty($lpoout_items) && $po->items && is_array($po->items)) {
            $lpoout_items = [];
            $total_amount = 0;

            foreach ($po->items as $idx => $item) {
                $quantity = floatval($item['quantity'] ?? 0);
                $cost = floatval($item['cost'] ?? 0);
                $item_total = $quantity * $cost;
                $total_amount += $item_total;

                $lpoout_items[] = [
                    'item_code' => $item['item_code'] ?? '',
                    'description' => $item['material_name'] ?? '',
                    'unit' => 'Unit',
                    'qty' => $quantity,
                    'unit_price' => $cost,
                    'total' => $item_total
                ];
            }
        } else {
            // Calculate total from stored items
            $total_amount = 0;
            foreach ($lpoout_items as $item) {
                $total_amount += floatval($item['total'] ?? 0);
            }
        }

        // Lump-sum pricing: the batch total is entered manually, not derived from row costs.
        // Override the computed subtotal so VAT and grand total flow from the typed amount.
        if ($isLumpSum) {
            $total_amount = floatval($po->lpout_manual_total ?? 0);
        }

        // Get LPO Out Type based on request type
        $lpo_out_type_id = 1; // Default type ID
        try {
            $requestType = $po->lpout_request_type ?? $po->request_type;
            
            // Map request type to LPO Out Type
            if ($requestType === 'project') {
                $type = \App\Models\LpoOutType::whereName('Project')->first();
            } elseif ($requestType === 'maintenance') {
                $type = \App\Models\LpoOutType::whereName('Maintenance')->first();
            } else {
                $type = \App\Models\LpoOutType::first();
            }
            
            if ($type) {
                $lpo_out_type_id = $type->id;
            }
        } catch (\Exception $e) {
            // Use default if query fails
        }

        // Prepare LPOUT data from stored fields in PO
        // Make sure all values are safe and don't trigger property access on null objects
        $lpoout_data = [
            'name' => !empty($po->lpout_name) ? $po->lpout_name : 'Purchase Order #' . ($po->id ?? 'N/A'),
            'lpo_invoice_no' => $originalLpoout ? $originalLpoout->lpo_invoice_no : $this->generateLpoInvoiceNo($po),
            'vendor_id' => $po->lpout_vendor_id ?? null,
            'lpo_out_type_id' => $lpo_out_type_id,
            // For 'project' type requests the form only submits quotation_id (labelled
            // "LPO In"), never project_id directly — derive the project from that quotation.
            'project_id' => $po->project_id ?: optional(optional($po->quotation)->project)->id,
            // Our own company TRN - never the vendor's
            'trn_no' => config('purchase-orders.company_trn'),
            'kindly_attn' => $po->lpout_kindly_attn ?? null,
            'date' => $po->lpout_date ?? now()->toDateString(),
            'amount' => $total_amount,
            'vat' => floatval($po->lpout_vat ?? 0),
            'total_amount' => $total_amount + (($total_amount * floatval($po->lpout_vat ?? 0)) / 100),
            'payment_type' => $po->lpout_payment_type ?? null,
            'cheque_date' => $po->lpout_cheque_date ?? null,
            'items' => $lpoout_items,
            // Carried over so the LPO print / PDF / vendor email render the same
            // columns the approvers saw on screen.
            'pricing_mode' => $isLumpSum ? 'lump' : 'unit',
            'has_item_code' => $po->hasItemCode(),
            'status' => 'Pending',
            'lpo_payment_preference_option' => $po->lpout_payment_preference_option ?? 'default',
            'lpo_payment_preference' => $po->lpout_payment_preference ?? null,
            'lpo_pdc_number_of_days' => $po->lpout_pdc_number_of_days ?? null,
            'lpo_pdc_payment_option' => $po->lpout_pdc_payment_option ?? null,
            'terms' => $po->lpout_terms ?? null,
            'file' => 'pending',
        ];

        if ($originalLpoout) {
            $lpoout_data['revision_number'] = $originalLpoout->revision_number + 1;
            $lpoout_data['parent_lpoout_id'] = $originalLpoout->id;
            $lpoout_data['is_latest_revision'] = true;
            $lpoout_data['revised_by'] = auth()->id();
            $lpoout_data['revision_reason'] = $po->other_info;
            $lpoout_data['revised_at'] = now();
        }

        // Create the new LPOUT first - only archive the original once it exists,
        // so a failed creation never leaves the LPO without a latest revision.
        $newLpoout = $this->lpooutRepository->create($lpoout_data);

        if ($originalLpoout) {
            $originalLpoout->is_latest_revision = false;
            $originalLpoout->save();
        }

        return $newLpoout;
    }

    /**
     * Generate a custom LPO invoice number.
     *
     * Format: {COMPANY3}-LPO-{COUNTER}-{MMYY}
     * Example: TELE-LPO-3470-0726
     *
     * COMPANY3 = first 3 uppercase letters of the company name.
     * COUNTER  = single global sequence, shared across all companies and months
     *            (see Lpoout::nextLpoCounter()).
     */
    private function generateLpoInvoiceNo($po): string
    {
        $company = null;
        $quotation = null;

        // Get quotation and company
        if (!empty($po->quotation_id)) {
            $quotation = $po->quotation;
        } elseif (!empty($po->project_id) && $po->project) {
            $quotation = $po->project->quotation;
        }

        if ($quotation) {
            $company = $quotation->company;
        }

        return \App\Models\Lpoout::buildLpoInvoiceNo(\App\Models\Lpoout::companyPrefix($company));
    }
}
