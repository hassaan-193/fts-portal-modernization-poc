<div class="card bg-white mb-3">
    <div class="card-header bg-light-blue-gradient">
        <h3 class="card-title"><i class="fa fa-tasks mr-1"></i> Department Review</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="160">Department</th><td>{{ $review->assigned_department }}</td></tr>
                    <tr><th>Assigned To</th><td>{{ optional($review->assignedUser)->name ?? '—' }}</td></tr>
                    <tr><th>Assignment Date</th><td>{{ optional($review->assignment_date)->format('d M Y') ?? '—' }}</td></tr>
                    <tr><th>Priority</th><td>{{ $review->priority ?? '—' }}</td></tr>
                    <tr><th>Response Deadline</th><td>{{ optional($review->response_deadline)->format('d M Y') ?? '—' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="160">Technical Status</th><td>{{ $review->technical_review_status ?? '—' }}</td></tr>
                    <tr><th>Site Visit?</th>
                        <td>
                            @if($review->site_visit_required)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                    @if($review->site_visit_required)
                    <tr><th>Visit Date</th><td>{{ optional($review->proposed_visit_date)->format('d M Y') ?? '—' }}</td></tr>
                    <tr><th>Visit Engineer</th><td>{{ optional($review->visitEngineer)->name ?? '—' }}</td></tr>
                    @endif
                    <tr><th>Reviewed By</th><td>{{ optional($review->reviewer)->name ?? '—' }}</td></tr>
                </table>
            </div>
        </div>
        @if($review->internal_comments)
        <div class="alert alert-light border mt-2">
            <strong>Internal Comments:</strong><br>{{ $review->internal_comments }}
        </div>
        @endif
        @if($review->site_visit_required && $review->visit_notes)
        <div class="alert alert-warning mt-2">
            <strong>Visit Notes:</strong><br>{{ $review->visit_notes }}
        </div>
        @endif
    </div>
</div>
