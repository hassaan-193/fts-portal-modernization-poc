@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Department Review — {{ $inquiry->inquiry_no }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.show', $inquiry->id) }}">{{ $inquiry->inquiry_no }}</a></li>
                    <li class="breadcrumb-item active">Department Review</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-maroon bg-white">
                <div class="card-header">
                    <h3 class="card-title">Review & Assignment Form</h3>
                </div>
                <div class="card-body">
                    @include('flash::message')
                    {!! Form::open(['route' => ['inquiries.department.store', $inquiry->id], 'method' => 'POST']) !!}

                    <h5 class="border-bottom pb-2 mb-3 text-maroon">Assignment</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('assigned_department', 'Assigned Department *') !!}
                                {!! Form::text('assigned_department', $inquiry->assigned_department, ['class' => 'form-control', 'required' => true]) !!}
                                @error('assigned_department') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('assigned_to', 'Assign To (User)') !!}
                                {!! Form::select('assigned_to', $users->prepend('— Select User —', ''), optional($inquiry->departmentReview)->assigned_to, ['class' => 'form-control']) !!}
                                @error('assigned_to') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('assignment_date', 'Assignment Date *') !!}
                                {!! Form::date('assignment_date', optional(optional($inquiry->departmentReview)->assignment_date)->toDateString() ?? now()->toDateString(), ['class' => 'form-control', 'required' => true]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('priority', 'Priority Override') !!}
                                {!! Form::select('priority', array_combine(\App\Models\Inquiry::PRIORITIES, \App\Models\Inquiry::PRIORITIES), optional($inquiry->departmentReview)->priority ?? $inquiry->priority, ['class' => 'form-control', 'placeholder' => '— Keep Current —']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('response_deadline', 'Response Deadline') !!}
                                {!! Form::date('response_deadline', optional(optional($inquiry->departmentReview)->response_deadline)->toDateString(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('technical_review_status', 'Technical Review Status') !!}
                                {!! Form::select('technical_review_status', [
                                    'Pending' => 'Pending',
                                    'In Progress' => 'In Progress',
                                    'Completed' => 'Completed',
                                ], optional($inquiry->departmentReview)->technical_review_status, ['class' => 'form-control', 'placeholder' => '— Select —']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('internal_comments', 'Internal Comments') !!}
                                {!! Form::textarea('internal_comments', optional($inquiry->departmentReview)->internal_comments, ['class' => 'form-control', 'rows' => 2]) !!}
                            </div>
                        </div>
                    </div>

                    <h5 class="border-bottom pb-2 mb-3 mt-3 text-maroon">Site Visit Decision</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    {!! Form::hidden('site_visit_required', 0) !!}
                                    <input type="checkbox" name="site_visit_required" value="1" class="custom-control-input" id="site_visit_required"
                                        {{ optional($inquiry->departmentReview)->site_visit_required ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="site_visit_required">Site Visit Required?</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="site_visit_section" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('proposed_visit_date', 'Proposed Visit Date') !!}
                                    {!! Form::date('proposed_visit_date', optional(optional($inquiry->departmentReview)->proposed_visit_date)->toDateString(), ['class' => 'form-control', 'id' => 'proposed_visit_date']) !!}
                                    @error('proposed_visit_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('visit_assigned_to', 'Visit Assigned To (Engineer)') !!}
                                    {!! Form::select('visit_assigned_to', $users->prepend('— Select Engineer —', ''), optional($inquiry->departmentReview)->visit_assigned_to, ['class' => 'form-control']) !!}
                                    @error('visit_assigned_to') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('visit_notes', 'Visit Notes') !!}
                                    {!! Form::textarea('visit_notes', optional($inquiry->departmentReview)->visit_notes, ['class' => 'form-control', 'rows' => 2]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {!! Form::submit('Save Review', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Quick inquiry summary --}}
            <div class="card bg-light">
                <div class="card-header"><h3 class="card-title">Inquiry Summary</h3></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>Client</th><td>{{ $inquiry->client_name }}</td></tr>
                        <tr><th>Type</th><td>{{ $inquiry->inquiry_type }}</td></tr>
                        <tr><th>Status</th><td><span class="badge badge-{{ $inquiry->statusBadgeClass() }}">{{ $inquiry->status }}</span></td></tr>
                        <tr><th>Priority</th><td>{{ $inquiry->priority }}</td></tr>
                        <tr><th>Expected Price</th><td>{{ $inquiry->expected_price ? 'AED '.number_format($inquiry->expected_price,2) : '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
$(document).ready(function () {
    function toggleSiteVisit() {
        if ($('#site_visit_required').is(':checked')) {
            $('#site_visit_section').show();
        } else {
            $('#site_visit_section').hide();
        }
    }
    toggleSiteVisit();
    $('#site_visit_required').on('change', toggleSiteVisit);
});
</script>
@endsection
@endsection
