<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use App\Models\InquiryActivity;
use App\Repositories\InquiryRepository;
use App\DataTables\InquirySalesDataTable;
use App\Http\Requests\InquiryFollowUpRequest;

class InquirySalesController extends AppBaseController
{
    /** @var InquiryRepository */
    private $inquiryRepository;

    public function __construct(InquiryRepository $inquiryRepo)
    {
        $this->middleware('auth');
        $this->inquiryRepository = $inquiryRepo;
    }

    /**
     * Display sales user's pipeline inquiries.
     */
    public function indexPipeline(InquirySalesDataTable $dataTable)
    {
        return $dataTable->render('inquiries.sales_pipeline');
    }

    // ─── Follow-ups ───────────────────────────────────────────────────────────

    /**
     * Store a new follow-up log entry.
     */
    public function storeFollowUp(InquiryFollowUpRequest $request, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeSales($inquiry);

        $followUp = InquiryFollowUp::create([
            'inquiry_id'      => $inquiry->id,
            'follow_up_date'  => $request->follow_up_date,
            'follow_up_notes' => $request->follow_up_notes,
            'client_feedback' => $request->client_feedback,
            'status'          => $request->status,
            'created_by'      => auth()->id(),
        ]);

        // Advance inquiry status based on follow-up outcome
        $statusMap = [
            'Won'             => Inquiry::STATUS_WON,
            'Lost'            => Inquiry::STATUS_LOST,
            'Under Follow-up' => Inquiry::STATUS_UNDER_FOLLOW_UP,
        ];

        $newStatus = $statusMap[$request->status] ?? Inquiry::STATUS_UNDER_FOLLOW_UP;
        $this->inquiryRepository->update(['status' => $newStatus], $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_FOLLOW_UP_ADDED, "Follow-up added. Status: {$request->status}.", ['follow_up_status' => $request->status]);

        Flash::success('Follow-up added successfully.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    /**
     * Close a Won or Lost inquiry.
     */
    public function close($inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeSales($inquiry);

        abort_unless(
            $inquiry->canTransitionTo(Inquiry::STATUS_CLOSED, auth()->user()->hasRole('Administration')),
            422,
            'Inquiry cannot be closed at its current stage.'
        );

        $this->inquiryRepository->update(['status' => Inquiry::STATUS_CLOSED], $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_STATUS_CHANGED, 'Inquiry closed.');

        Flash::success('Inquiry closed.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function authorizeSales(Inquiry $inquiry): void
    {
        $user = auth()->user();

        if ($user->hasRole('Administration')) {
            return;
        }

        abort_unless($user->hasRole('Sales'), 403, 'Only Sales users can access this section.');
    }
}
