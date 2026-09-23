<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\InquiryEngineerReport;
use App\Models\InquiryActivity;
use App\Repositories\InquiryRepository;
use App\DataTables\InquiryEngineerDataTable;
use App\Http\Requests\InquiryEngineerReportRequest;

class InquiryEngineerController extends AppBaseController
{
    /** @var InquiryRepository */
    private $inquiryRepository;

    public function __construct(InquiryRepository $inquiryRepo)
    {
        $this->middleware('auth');
        $this->inquiryRepository = $inquiryRepo;
    }

    /**
     * Display engineer's assigned site visits.
     */
    public function indexMyVisits(InquiryEngineerDataTable $dataTable)
    {
        return $dataTable->render('inquiries.engineer_visits');
    }

    /**
     * Show the engineer report submission form.
     * Only accessible when status = Site Visit Pending and user is assigned engineer.
     */
    public function create($inquiryId)
    {
        $inquiry = Inquiry::with('departmentReview')->findOrFail($inquiryId);

        $this->authorizeEngineer($inquiry);

        return view('inquiries.engineer_report', compact('inquiry'));
    }

    /**
     * Submit the engineer report and advance the workflow.
     */
    public function store(InquiryEngineerReportRequest $request, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $this->authorizeEngineer($inquiry);

        $report = InquiryEngineerReport::updateOrCreate(
            ['inquiry_id' => $inquiry->id],
            array_merge($request->all(), [
                'inquiry_id'   => $inquiry->id,
                'submitted_by' => auth()->id(),
            ])
        );

        // Attach site visit files
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $report->addMedia($file)->withCustomProperties(['context' => 'site_visit'])->toMediaCollection('site_visit');
            }
            $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_FILE_UPLOADED, 'Site visit files uploaded.', ['context' => 'site_visit']);
        }

        // Advance status to Site Visit Done
        $this->inquiryRepository->update(['status' => Inquiry::STATUS_SITE_VISIT_DONE], $inquiry->id);
        $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_ENGINEER_REPORT, 'Engineer report submitted. Site visit marked as completed.');

        Flash::success('Engineer report submitted successfully.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function authorizeEngineer(Inquiry $inquiry): void
    {
        $user = auth()->user();

        if ($user->hasRole('Administration')) {
            return;
        }

        abort_unless(
            $inquiry->status === Inquiry::STATUS_SITE_VISIT_PENDING,
            422,
            'Site visit report can only be submitted when status is "Site Visit Pending".'
        );

        $review = $inquiry->departmentReview;
        abort_unless(
            $review && (int) $review->visit_assigned_to === $user->id,
            403,
            'You are not the assigned engineer for this site visit.'
        );
    }
}
