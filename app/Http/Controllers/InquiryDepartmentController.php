<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\InquiryDepartmentReview;
use App\Models\InquiryActivity;
use App\Models\InquiryQuotation;
use App\Repositories\InquiryRepository;
use App\DataTables\InquiryDepartmentDataTable;
use App\Http\Requests\InquiryDepartmentReviewRequest;
use App\Http\Requests\InquirySalesQuotationRequest;
use App\Notifications\InquiryNotification;
use App\User;

class InquiryDepartmentController extends AppBaseController
{
    /** @var InquiryRepository */
    private $inquiryRepository;

    public function __construct(InquiryRepository $inquiryRepo)
    {
        $this->middleware('auth');
        $this->inquiryRepository = $inquiryRepo;
    }

    /**
     * Display department user's assigned inquiries.
     */
    public function indexMyInquiries(InquiryDepartmentDataTable $dataTable)
    {
        return $dataTable->render('inquiries.department_inquiries');
    }

    /**
     * Show the department review / assignment form.
     */
    public function create($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);

        $this->authorizeReview($inquiry);

        $users = User::orderBy('name')->pluck('name', 'id');

        return view('inquiries.department_review', compact('inquiry', 'users'));
    }

    /**
     * Save the department review and advance the workflow.
     */
    public function store(InquiryDepartmentReviewRequest $request, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeReview($inquiry);

        $input                = $request->all();
        $input['inquiry_id']  = $inquiry->id;
        $input['reviewed_by'] = auth()->id();
        $input['assignment_date'] = $input['assignment_date'] ?? now()->toDateString();

        // Upsert – only one review record per inquiry (latest is always used)
        InquiryDepartmentReview::updateOrCreate(
            ['inquiry_id' => $inquiry->id],
            $input
        );

        // Determine next workflow status
        $siteVisitRequired = (bool) ($input['site_visit_required'] ?? false);

        if ($siteVisitRequired) {
            $newStatus = Inquiry::STATUS_SITE_VISIT_PENDING;
            $logAction = InquiryActivity::ACTION_SITE_VISIT_SCHEDULED;
            $logDesc   = 'Department review submitted. Site visit scheduled.';
        } else {
            $newStatus = Inquiry::STATUS_UNDER_REVIEW;
            $logAction = InquiryActivity::ACTION_DEPARTMENT_REVIEW;
            $logDesc   = 'Department review submitted.';
        }

        // Override priority if provided
        $updateData = ['status' => $newStatus];
        if (!empty($input['priority'])) {
            $updateData['priority'] = $input['priority'];
        }

        $this->inquiryRepository->update($updateData, $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), $logAction, $logDesc, [
            'assigned_to'         => $input['assigned_to'] ?? null,
            'site_visit_required' => $siteVisitRequired,
        ]);

        // Notify assigned user if set
        if (!empty($input['assigned_to'])) {
            $assignee = User::find($input['assigned_to']);
            if ($assignee) {
                $assignee->notify(new InquiryNotification(
                    'Inquiry Assigned to You',
                    "Inquiry {$inquiry->inquiry_no} has been assigned to you.",
                    $inquiry
                ));
            }
        }

        // Notify engineer for site visit
        if ($siteVisitRequired && !empty($input['visit_assigned_to'])) {
            $engineer = User::find($input['visit_assigned_to']);
            if ($engineer) {
                $engineer->notify(new InquiryNotification(
                    'Site Visit Assigned',
                    "You have been assigned a site visit for inquiry {$inquiry->inquiry_no}.",
                    $inquiry
                ));

                // Send WhatsApp notification to engineer
                try {
                    $whatsAppService = app(\App\Services\WhatsAppService::class);
                    $whatsAppService->sendEngineerAssignmentNotification($inquiry, $input['visit_assigned_to']);
                } catch (\Exception $e) {
                    \Log::error('Failed to send WhatsApp engineer assignment notification', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
                }
            }
        }

        Flash::success('Department review saved successfully.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    /**
     * Forward inquiry to the sales team.
     */
    public function forwardToSales($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeReview($inquiry);

        abort_unless(
            $inquiry->canTransitionTo(Inquiry::STATUS_SENT_TO_SALES, auth()->user()->hasRole('Administration')),
            422,
            'This inquiry cannot be forwarded to sales at its current stage.'
        );

        $this->inquiryRepository->update(['status' => Inquiry::STATUS_SENT_TO_SALES], $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_SENT_TO_SALES, 'Inquiry forwarded to Sales.');

        // Notify sales users
        $salesUsers = User::role('Sales')->get();
        foreach ($salesUsers as $user) {
            $user->notify(new InquiryNotification(
                'New Inquiry Sent to Sales',
                "Inquiry {$inquiry->inquiry_no} has been forwarded to the sales team.",
                $inquiry
            ));
        }

        Flash::success('Inquiry forwarded to Sales.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function authorizeReview(Inquiry $inquiry): void
    {
        $user = auth()->user();

        if ($user->hasRole('Administration')) {
            return;
        }

        // Department users and sales cannot submit reviews
        abort_unless(
            !$user->hasRole('Sales') && !$user->hasRole('Engineer'),
            403,
            'Only department users can submit reviews.'
        );

        $userRoles = $user->getRoleNames()->toArray();
        abort_unless(in_array($inquiry->assigned_department, $userRoles), 403);
    }

    // ─── Quotation ────────────────────────────────────────────────────────────

    /**
     * Show the quotation creation form.
     */
    public function createQuotation($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeReview($inquiry);

        abort_unless(
            $inquiry->canTransitionTo(Inquiry::STATUS_QUOTATION_CREATED, auth()->user()->hasRole('Administration')),
            422,
            'Quotation can only be created when inquiry is in "Under Review" or "Site Visit Done" stage.'
        );

        return view('inquiries.department_quotation', compact('inquiry'));
    }

    /**
     * Store the quotation created by department.
     */
    public function storeQuotation(InquirySalesQuotationRequest $request, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeReview($inquiry);

        abort_unless(
            $inquiry->canTransitionTo(Inquiry::STATUS_QUOTATION_CREATED, auth()->user()->hasRole('Administration')),
            422
        );

        $quotation = InquiryQuotation::updateOrCreate(
            ['inquiry_id' => $inquiry->id],
            array_merge($request->all(), [
                'inquiry_id' => $inquiry->id,
                'created_by' => auth()->id(),
            ])
        );

        // Attach quotation file
        if ($request->hasFile('quotation_file')) {
            $quotation->addMedia($request->file('quotation_file'))
                ->withCustomProperties(['context' => 'quotation'])
                ->toMediaCollection('quotation');
            $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_FILE_UPLOADED, 'Quotation file uploaded.', ['context' => 'quotation']);
        }

        $this->inquiryRepository->update(['status' => Inquiry::STATUS_QUOTATION_CREATED], $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_QUOTATION_CREATED, "Quotation created. Amount: {$quotation->quotation_amount}.");

        // Notify sales users
        $salesUsers = User::role('Sales')->get();
        foreach ($salesUsers as $user) {
            $user->notify(new InquiryNotification(
                'New Quotation Ready',
                "Inquiry {$inquiry->inquiry_no} has a quotation ready for follow-up.",
                $inquiry
            ));
        }

        // Send WhatsApp notification for quotation
        try {
            $whatsAppService = app(\App\Services\WhatsAppService::class);
            $whatsAppService->sendQuotationCreatedNotification($inquiry, $quotation);
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp quotation notification', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        Flash::success('Quotation created successfully.');

        return redirect(route('inquiries.show', $inquiry->id));
    }
}
