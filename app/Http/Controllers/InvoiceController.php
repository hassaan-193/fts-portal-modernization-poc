<?php

namespace App\Http\Controllers;

use Flash;
use Response;
use Mail;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\DataTables\InvoiceDataTable;
use App\Repositories\InvoiceRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\DataTables\InvoiceRequestALLDataTable;
use App\Repositories\InvoiceRequestRepository;

class InvoiceController extends AppBaseController
{
    /** @var  InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @param InvoiceDataTable $invoiceDataTable
     * @return Response
     */
public function index(InvoiceDataTable $invoiceDataTable, Request $request)
{
    // Get all companies that have pending invoices
    $companies = \App\Models\Company::whereHas('quotations.invoices', function($q) {
            $q->where('status', 0); // Only companies with pending invoices
        })
        ->orderBy('name', 'asc')
        ->get(['id', 'name']);
    
    // Calculate stats for selected company (PENDING invoices only)
    $companyStats = null;
    if($request->has('company_id') && $request->company_id != '') {
        $companyStats = \App\Models\Invoice::where('status', 0) // Only pending
            ->whereHas('quotation', function($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->selectRaw('COUNT(*) as total_pending_invoices, SUM(total_amount) as total_pending_amount')
            ->first();
        
        $selectedCompany = \App\Models\Company::find($request->company_id);
        $companyStats->company_name = $selectedCompany ? $selectedCompany->name : '';
    }
    
    return $invoiceDataTable->render('invoices.index', [
        'type' => $request->exists('status') ? $request->get('status') : '',
        'companies' => $companies,
        'companyStats' => $companyStats,
        'selectedCompany' => $request->company_id ?? '',
        'multiplePendingFilter' => $request->multiple_pending ?? ''
    ]);
}


    
    /**
     * Show the form for creating a new Invoice.
     *
     * @return Response
     */
    public function create($request, InvoiceRequestRepository $invoice_request)
    {
        $invoiceRequest = $invoice_request->find($request);
        if(!$invoiceRequest){
            Flash::error(__('models/invoice_requests.singular').' '.__('messages.not_found'));
            return redirect()->back();
        }
        // load products
        $invoiceRequest->load(['requestable', 'request_products']);
        return view('invoices.create',compact('invoiceRequest'));
    }

    /**
     * Store a newly created Invoice in storage.
     *
     * @param CreateInvoiceRequest $request
     *
     * @return Response
     */
    public function store(CreateInvoiceRequest $request)
    {
        $input = $request->all();

        $invoice = $this->invoiceRepository->createInvoice($input,$request);

        Flash::success(__('messages.saved', ['model' => __('models/invoices.singular')]));

        return redirect(route('invoices.index'));
    }

    /**
     * Display the specified Invoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $invoice = $this->invoiceRepository->find($id);

        if (empty($invoice)) {
            Flash::error(__('models/invoices.singular').' '.__('messages.not_found'));

            return redirect(route('invoices.index'));
        }
        return view('invoices.show')->with('invoice', $invoice);
    }

    /**
     * Show the form for editing the specified Invoice.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $invoice = $this->invoiceRepository->find($id);

        if (empty($invoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoices.singular')]));

            return redirect(route('invoices.index'));
        }

        return view('invoices.edit')->with('invoice', $invoice);
    }

    /**
     * Update the specified Invoice in storage.
     *
     * @param  int              $id
     * @param UpdateInvoiceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateInvoiceRequest $request)
    {
        $invoice = $this->invoiceRepository->find($id);

        if (empty($invoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoices.singular')]));

            return redirect(route('invoices.index'));
        }

        $invoice = $this->invoiceRepository->updateInvoice($request->all(), $id, $request);

        Flash::success(__('messages.updated', ['model' => __('models/invoices.singular')]));

        return redirect(route('invoices.index'));
    }

    /**
     * Remove the specified Invoice from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $invoice = $this->invoiceRepository->find($id);

        if (empty($invoice)) {
            Flash::error(__('messages.not_found', ['model' => __('models/invoices.singular')]));

            return redirect(route('invoices.index'));
        }

        $status = $this->invoiceRepository->delete($id);
        if($status)
            Flash::success(__('messages.deleted', ['model' => __('models/invoices.singular')]));
        else
            Flash::error(__('messages.permisssion_error'));

        return redirect(route('invoices.index'));
    }
    /**
     * Display a listing of the InvoiceRequest.
     *
     * @param InvoiceRequestDataTable $invoiceRequestDataTable
     * @return Response
     */
    public function request_invoices(InvoiceRequestALLDataTable $invoiceRequestDataTable, Request $request)
    {
        return $invoiceRequestDataTable->render('invoice_requests.index', with([
            'type' => $request->exists('status') ? $request->get('status') : '',
        ]));
    }

    /**
     * Show form for approving and emailing invoice request
     *
     * @param int $requestId
     * @return Response
     */
    public function showApproveEmailForm($requestId, InvoiceRequestRepository $invoice_request)
    {
        $invoiceRequest = $invoice_request->find($requestId);
        if (!$invoiceRequest) {
            Flash::error(__('models/invoice_requests.singular') . ' ' . __('messages.not_found'));
            return redirect()->back();
        }

        $invoiceRequest->load(['requestable', 'request_products']);
        
        return view('invoices.approve-email-form', compact('invoiceRequest'));
    }

    /**
     * Approve invoice request and send email
     *
     * @param ApproveAndEmailInvoiceRequest $request
     * @return Response
     */
    public function approveAndEmail(\App\Http\Requests\ApproveAndEmailInvoiceRequest $request, InvoiceRequestRepository $invoice_request)
    {
        $invoiceRequestId = $request->input('invoice_request_id');
        $letterheadType = $request->input('letterhead_type');
        $ccEmails = $request->getMergedCcEmails();
        $attachments = $this->getAttachmentData($request);

        try {
            // Find invoice request
            $invoiceReq = $invoice_request->find($invoiceRequestId);
            if (!$invoiceReq) {
                Flash::error(__('models/invoice_requests.singular') . ' ' . __('messages.not_found'));
                return redirect()->back();
            }

            // Load request products
            $invoiceReq->load(['requestable', 'request_products']);

            // Build invoice data from invoice request
            $invoiceData = [
                'invoice_no' => $this->generateInvoiceNumber(),
                'invoice_type_id' => $invoiceReq->requestable->invoice_type_id ?? 1,
                'quotation_id' => $invoiceReq->requestable_id,
                'invoice_request_id' => $invoiceReq->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => $invoiceReq->delivery_date ?? now()->format('Y-m-d'),
                'delivery_date' => $invoiceReq->delivery_date ?? now()->format('Y-m-d'),
                'payment_terms' => $invoiceReq->payment_terms,
                'currency' => $invoiceReq->requestable->currency ?? 'AED',
                'invoice_bank_id' => $invoiceReq->requestable->invoice_bank_id ?? 1,
                'note1' => $invoiceReq->requestable->note1 ?? '',
                'note2' => $invoiceReq->requestable->note2 ?? '',
                'amount_in_word' => 'To be filled',
                'vat' => 5,
                'product' => [],
                'unit' => [],
                'qty' => [],
                'rate' => [],
                'amount' => [],
                'description' => [],
                'service_amount' => [],
            ];

            // Populate product arrays from invoice request products
            foreach ($invoiceReq->request_products as $product) {
                $invoiceData['product'][] = $product->product;
                $invoiceData['unit'][] = $product->unit ?? '';
                $invoiceData['qty'][] = $product->qty ?? 1;
                $invoiceData['rate'][] = $product->rate ?? 0;
                $invoiceData['amount'][] = $product->amount ?? 0;
            }

            // Create mock request object for repository
            $mockRequest = new \Illuminate\Http\Request();
            $mockRequest->merge($invoiceData);

            // Create invoice via repository
            $result = $this->invoiceRepository->createInvoice($invoiceData, $mockRequest);
            
            if ($result instanceof Exception) {
                throw $result;
            }

            // Fetch the created invoice
            $invoice = \App\Models\Invoice::where('invoice_request_id', $invoiceReq->id)
                ->orderBy('id', 'desc')
                ->first();

            if (!$invoice) {
                throw new \Exception('Invoice was created but could not be retrieved.');
            }

            $emailSent = false;
            $emailError = null;
            $pdfPath = null;

            // Send email if company has valid email
            if ($invoice->quotation->company) {
                $company = $invoice->quotation->company;
                $recipientEmail = $company->email ?? $company->billing_email ?? null;

                if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    try {
                        // Log email details
                        \Log::info("Invoice email sending", [
                            'invoice_no' => $invoice->invoice_no,
                            'to_email' => $recipientEmail,
                            'company_name' => $company->name,
                            'cc_emails' => $ccEmails,
                            'letterhead_type' => $letterheadType,
                            'from_email' => env('MAIL_FROM_ADDRESS_ACCOUNTS', 'accounts.rak@example.com'),
                        ]);

                        // Generate invoice PDF
                        $pdfService = new \App\Services\InvoicePdfService();
                        $pdfPath = $pdfService->generateInvoicePdf($invoice, $letterheadType);
                        
                        \Log::info("PDF generated at: {$pdfPath}, exists: " . (file_exists($pdfPath) ? 'yes' : 'no'));

                        // Send email using simple mailable with invoice PDF via Mail facade
                        \Mail::send(new \App\Mail\InvoiceEmailSimpleMailable($invoice, $company, $pdfPath, $ccEmails, $attachments));

                        $emailSent = true;

                        // Log the email send
                        \Log::info("Invoice email sent successfully", [
                            'invoice_no' => $invoice->invoice_no,
                            'recipient' => $recipientEmail,
                        ]);

                    } catch (\Exception $e) {
                        $emailError = $e->getMessage();
                        \Log::error("Invoice email failed for Invoice #{$invoice->invoice_no}: " . $e->getMessage(), [
                            'exception' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ]);
                    } finally {
                        // Clean up PDF
                        if ($pdfPath) {
                            $pdfService = new \App\Services\InvoicePdfService();
                            $pdfService->deletePdf($pdfPath);
                        }
                    }
                } elseif (!$recipientEmail) {
                    $emailError = 'No email address found for company.';
                } else {
                    $emailError = 'Company email address is invalid.';
                }
            }

            // Build flash message
            $message = __('messages.saved', ['model' => __('models/invoices.singular')]);
            if ($emailSent) {
                $message .= ' and email sent successfully.';
                Flash::success($message);
            } else {
                $errorMsg = $emailError ? " Email could not be sent: {$emailError}" : '';
                Flash::warning($message . $errorMsg);
            }

            // Report invalid CC emails if any
            if ($request->has('invalid_cc_emails') && !empty($request->get('invalid_cc_emails'))) {
                $invalid = implode(', ', $request->get('invalid_cc_emails'));
                Flash::warning("Some CC emails were invalid and not sent: {$invalid}");
            }

            return redirect(route('invoices.index'));
        } catch (\Exception $e) {
            \Log::error("Approve and email failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            Flash::error("Error: " . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Generate unique invoice number
     *
     * @return string
     */
    private function generateInvoiceNumber()
    {
        $year = now()->year;
        $month = now()->month;
        
        // Get last invoice for this month
        $lastInvoice = \App\Models\Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? intval(substr($lastInvoice->invoice_no, -4)) + 1 : 1;
        
        return sprintf('INV-%04d-%04d', $month, $sequence);
    }

    /**
     * Send invoice email for already-created invoice
     *
     * @param int $invoiceId
     * @return Response
     */
    public function sendInvoiceEmail(Request $request, $invoiceId)
    {
        try {
            // First, validate the request
            $this->validate($request, [
                'letterhead_type' => 'required|in:fts,experts,ftsits',
                'cc_emails' => 'nullable',
                'fixed_cc_emails' => 'nullable|array',
                'fixed_cc_emails.*' => 'email',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240',
            ]);

            $invoice = $this->invoiceRepository->find($invoiceId);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found.'
                ], 404);
            }

            $company = $invoice->quotation->company;
            $letterheadType = $request->input('letterhead_type');
            
            // Handle CC emails - convert array to string if needed, handle null values
            $ccEmailsInput = $request->input('cc_emails', '');
            if (is_null($ccEmailsInput)) {
                $ccEmailsInput = '';
            } elseif (is_array($ccEmailsInput)) {
                $ccEmailsInput = implode(',', array_filter($ccEmailsInput));
            }
            $ccEmails = $this->parseCcEmails($ccEmailsInput);
            
            // Get fixed CC emails
            $fixedCcEmails = $request->input('fixed_cc_emails', []);
            if (!is_array($fixedCcEmails)) {
                $fixedCcEmails = [];
            }
            
            // Merge and remove duplicates
            $mergedCcEmails = array_unique(array_merge($ccEmails, $fixedCcEmails));
            $ccEmails = array_filter($mergedCcEmails, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            
            // Get uploaded attachments with metadata
            $attachments = $this->getAttachmentData($request);

            // Validate recipient email
            $recipientEmail = $company->email ?? $company->billing_email ?? null;
            if (!$recipientEmail || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid email address found for this company.'
                ], 400);
            }

            // Generate invoice PDF
            $pdfService = new \App\Services\InvoicePdfService();
            $pdfPath = $pdfService->generateInvoicePdf($invoice, $letterheadType);

            $emailSent = false;
            
            try {
                // Create and send the mailable using Mail facade
                $mailable = new \App\Mail\InvoiceEmailSimpleMailable($invoice, $company, $pdfPath, $ccEmails, $attachments);
                \Mail::send($mailable);
                $emailSent = true;
                
                \Log::info("Invoice email sent successfully for Invoice #{$invoice->invoice_no}");
            } catch (\Exception $mailException) {
                \Log::error("Mail send failed: " . $mailException->getMessage(), [
                    'exception' => get_class($mailException),
                    'file' => $mailException->getFile(),
                    'line' => $mailException->getLine(),
                ]);
                throw $mailException;
            } finally {
                // Always clean up PDF
                if ($pdfPath && file_exists($pdfPath)) {
                    $pdfService->deletePdf($pdfPath);
                }
            }

            // Check for invalid CC emails
            $ccEmailsForValidation = $request->input('cc_emails', '');
            if (is_null($ccEmailsForValidation)) {
                $ccEmailsForValidation = '';
            } elseif (is_array($ccEmailsForValidation)) {
                $ccEmailsForValidation = implode(',', array_filter($ccEmailsForValidation));
            }
            $invalidEmails = $this->getInvalidCcEmails($ccEmailsForValidation);
            
            $message = 'Invoice email sent successfully.';
            if (!empty($invalidEmails)) {
                $message .= ' Some CC emails were invalid: ' . implode(', ', $invalidEmails);
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error("Send invoice email failed: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract attachment file data from request
     *
     * @param Request $request
     * @return array
     */
    private function getAttachmentData(Request $request): array
    {
        $attachments = [];
        
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            
            // Handle both single file and array of files
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->getPathname();
                    if (is_string($path) && !empty($path)) {
                        $attachments[] = [
                            'path' => $path,
                            'name' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ];
                    }
                }
            }
        }
        
        return $attachments;
    }

    /**
     * Extract attachment file paths from request
     *
     * @param Request $request
     * @return array
     */
    private function getAttachmentPaths(Request $request): array
    {
        $attachments = [];
        
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            
            // Handle both single file and array of files
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->getPathname();
                    if (is_string($path) && !empty($path)) {
                        $attachments[] = $path;
                    }
                }
            }
        }
        
        return $attachments;
    }

    /**
     * Parse and validate CC emails
     *
     * @param string $ccString
     * @return array
     */
    private function parseCcEmails(string $ccString): array
    {
        if (!$ccString) {
            return [];
        }

        $emails = array_map('trim', explode(',', $ccString));
        return array_filter($emails, function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * Get invalid CC emails from string
     *
     * @param string $ccString
     * @return array
     */
    private function getInvalidCcEmails(string $ccString): array
    {
        if (!$ccString) {
            return [];
        }

        $emails = array_map('trim', explode(',', $ccString));
        return array_filter($emails, function ($email) {
            return !empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }
}
