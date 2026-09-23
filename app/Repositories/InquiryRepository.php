<?php

namespace App\Repositories;

use App\Models\Inquiry;
use App\Models\InquiryActivity;
use App\Models\InquiryRoutingConfig;

class InquiryRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'inquiry_no',
        'client_name',
        'phone',
        'email',
        'inquiry_type',
        'status',
        'assigned_department',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Inquiry::class;
    }

    /**
     * Create a new inquiry, auto-generate inquiry_no, apply routing, log activity.
     */
    public function create($input)
    {
        $input['inquiry_no']  = Inquiry::generateInquiryNo();
        $input['created_by']  = auth()->id();
        $input['status']      = Inquiry::STATUS_NEW;
        $input['assigned_department'] = InquiryRoutingConfig::departmentFor($input['inquiry_type'] ?? '');

        $inquiry = $this->model->newInstance($input);
        $inquiry->save();

        $this->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_CREATED, 'Inquiry created.');

        return $inquiry;
    }

    /**
     * Update inquiry fields and log a status change if status differs.
     */
    public function update($input, $id)
    {
        $inquiry = $this->model->newQuery()->findOrFail($id);

        $oldStatus = $inquiry->status;
        $inquiry->fill($input)->save();

        if (isset($input['status']) && $input['status'] !== $oldStatus) {
            $this->logActivity($inquiry, auth()->user(), InquiryActivity::ACTION_STATUS_CHANGED,
                "Status changed from {$oldStatus} to {$input['status']}.",
                ['old_status' => $oldStatus, 'new_status' => $input['status']]
            );
        }

        return $inquiry;
    }

    /**
     * Record an activity entry for an inquiry.
     */
    public function logActivity(Inquiry $inquiry, $user, string $action, string $description, array $metadata = []): InquiryActivity
    {
        return InquiryActivity::create([
            'inquiry_id'  => $inquiry->id,
            'user_id'     => $user->id,
            'action'      => $action,
            'description' => $description,
            'metadata'    => $metadata ?: null,
        ]);
    }

    /**
     * Scope query by authenticated user's role for access control.
     *
     * - Admin: all inquiries
     * - Department user: inquiries assigned to their department
     * - Engineer: inquiries where they are the visit engineer
     * - Sales: inquiries in sales-stage statuses
     */
    public function scopedQuery($user)
    {
        $query = $this->model->newQuery();

        if ($user->hasRole('Administration')) {
            return $query;
        }

        if ($user->hasRole('Sales')) {
            return $query->whereIn('status', [
                Inquiry::STATUS_SENT_TO_SALES,
                Inquiry::STATUS_QUOTATION_CREATED,
                Inquiry::STATUS_UNDER_FOLLOW_UP,
                Inquiry::STATUS_WON,
                Inquiry::STATUS_LOST,
                Inquiry::STATUS_CLOSED,
            ]);
        }

        if ($user->hasRole('Engineer')) {
            return $query->whereHas('departmentReview', function ($q) use ($user) {
                $q->where('visit_assigned_to', $user->id);
            });
        }

        // Department user – filter by their department name stored in their profile/role
        // Convention: role name equals department name (e.g. "Project Team", "Maintenance Team")
        $userRoles = $user->getRoleNames()->toArray();
        return $query->whereIn('assigned_department', $userRoles);
    }
}
