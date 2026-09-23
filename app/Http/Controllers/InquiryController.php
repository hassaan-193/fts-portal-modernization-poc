<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\InquiryActivity;
use App\Repositories\InquiryRepository;
use App\DataTables\InquiryDataTable;
use App\Http\Requests\CreateInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Notifications\InquiryNotification;
use App\User;

class InquiryController extends AppBaseController
{
    /** @var InquiryRepository */
    private $inquiryRepository;

    public function __construct(InquiryRepository $inquiryRepo)
    {
        $this->middleware('auth');
        $this->inquiryRepository = $inquiryRepo;
    }

    /**
     * Display a listing of inquiries scoped to the authenticated user's role.
     */
    public function index(InquiryDataTable $dataTable)
    {
        return $dataTable->render('inquiries.index');
    }

    /**
     * Show the form for creating a new Inquiry.
     */
    public function create()
    {
        return view('inquiries.create');
    }

    /**
     * Store a newly created Inquiry.
     */
    public function store(CreateInquiryRequest $request)
    {
        $inquiry = $this->inquiryRepository->create($request->all());

        // Attach uploaded files with context "inquiry"
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $inquiry->addMedia($file)->withCustomProperties(['context' => 'inquiry'])->toMediaCollection('inquiry');
            }
            $this->inquiryRepository->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_FILE_UPLOADED, 'Files attached to inquiry.');
        }

        // Notify department users
        $this->notifyDepartment($inquiry, 'New Inquiry Assigned', "A new inquiry ({$inquiry->inquiry_no}) has been assigned to {$inquiry->assigned_department}.");

        // Send WhatsApp notification to relevant staff
        try {
            $whatsAppService = app(\App\Services\WhatsAppService::class);
            $whatsAppService->sendNewInquiryNotification($inquiry);
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp inquiry notification', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        Flash::success('Inquiry created successfully.');

        return redirect(route('inquiries.show', $inquiry->id));
    }

    /**
     * Display the specified Inquiry with all workflow stages.
     */
    public function show($id)
    {
        $inquiry = Inquiry::with([
            'creator',
            'departmentReview.assignedUser',
            'departmentReview.visitEngineer',
            'engineerReport.engineer',
            'quotation.creator',
            'followUps.creator',
            'activities.user',
        ])->findOrFail($id);

        $this->authorizeAccess($inquiry);

        $users = User::orderBy('name')->pluck('name', 'id');

        return view('inquiries.show', compact('inquiry', 'users'));
    }

    /**
     * Show the form for editing the specified Inquiry.
     */
    public function edit($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $this->authorizeAccess($inquiry);

        // Only admin or creator can edit core inquiry data
        abort_unless(
            auth()->user()->hasRole('Administration') || $inquiry->created_by === auth()->id(),
            403,
            'You are not authorised to edit this inquiry.'
        );

        return view('inquiries.edit', compact('inquiry'));
    }

    /**
     * Update the specified Inquiry.
     */
    public function update(UpdateInquiryRequest $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        abort_unless(
            auth()->user()->hasRole('Administration') || $inquiry->created_by === auth()->id(),
            403
        );

        $this->inquiryRepository->update($request->all(), $id);

        Flash::success('Inquiry updated successfully.');

        return redirect(route('inquiries.show', $id));
    }

    /**
     * Remove the specified Inquiry (admin only).
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->hasRole('Administration'), 403);

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        Flash::success('Inquiry deleted.');

        return redirect(route('inquiries.index'));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Enforce role-based access: engineers only see their site visits,
     * sales only see sales-stage inquiries, department users see their department.
     */
    private function authorizeAccess(Inquiry $inquiry): void
    {
        $user = auth()->user();

        if ($user->hasRole('Administration')) {
            return;
        }

        if ($user->hasRole('Sales')) {
            abort_unless(in_array($inquiry->status, [
                Inquiry::STATUS_SENT_TO_SALES,
                Inquiry::STATUS_QUOTATION_CREATED,
                Inquiry::STATUS_UNDER_FOLLOW_UP,
                Inquiry::STATUS_WON,
                Inquiry::STATUS_LOST,
                Inquiry::STATUS_CLOSED,
            ]), 403);
            return;
        }

        if ($user->hasRole('Engineer')) {
            $review = $inquiry->departmentReview;
            abort_unless($review && (int)$review->visit_assigned_to === $user->id, 403);
            return;
        }

        // Department user
        $userRoles = $user->getRoleNames()->toArray();
        abort_unless(in_array($inquiry->assigned_department, $userRoles), 403);
    }

    /**
     * Send in-app notifications to all users whose role matches the department.
     */
    private function notifyDepartment(Inquiry $inquiry, string $title, string $body): void
    {
        $department = $inquiry->assigned_department;
        if (!$department) {
            return;
        }

        $users = User::role($department)->get();
        foreach ($users as $user) {
            $user->notify(new InquiryNotification($title, $body, $inquiry));
        }
    }
}
