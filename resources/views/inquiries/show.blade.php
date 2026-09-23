@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Inquiry — {{ $inquiry->inquiry_no }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item active">{{ $inquiry->inquiry_no }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    <div class="row">
        {{-- ─── Left Column ─── --}}
        <div class="col-md-8">

            {{-- Inquiry Header Card --}}
            <div class="card card-maroon bg-white mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Inquiry Details</h3>
                    <div>
                        <span class="badge badge-{{ $inquiry->statusBadgeClass() }} mr-1">{{ $inquiry->status }}</span>
                        @php $pmap = ['Low' => 'success', 'Medium' => 'warning', 'High' => 'danger']; @endphp
                        <span class="badge badge-{{ $pmap[$inquiry->priority] ?? 'secondary' }}">{{ $inquiry->priority }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="140">Inquiry No</th><td>{{ $inquiry->inquiry_no }}</td></tr>
                                <tr><th>Date</th><td>{{ $inquiry->created_at->format('d M Y') }}</td></tr>
                                <tr><th>Created By</th><td>{{ $inquiry->creator->name ?? '—' }}</td></tr>
                                <tr><th>Department</th><td>{{ $inquiry->assigned_department ?? '—' }}</td></tr>
                                <tr><th>Source</th><td>{{ $inquiry->source }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="140">Client</th><td>{{ $inquiry->client_name }}</td></tr>
                                <tr><th>Phone</th><td>{{ $inquiry->phone }}</td></tr>
                                <tr><th>Email</th><td>{{ $inquiry->email ?? '—' }}</td></tr>
                                <tr><th>Location</th><td>{{ $inquiry->location ?? '—' }}</td></tr>
                                <tr><th>Project</th><td>{{ $inquiry->project ?? '—' }}</td></tr>
                                <tr><th>Type</th>
                                    <td>{{ $inquiry->inquiry_type }}
                                        @if($inquiry->inquiry_type === 'Other' && $inquiry->other_type)
                                            <small class="text-muted">({{ $inquiry->other_type }})</small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <small class="text-muted">Expected Price</small>
                            <p>{{ $inquiry->expected_price ? 'AED ' . number_format($inquiry->expected_price, 2) : '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Follow-up Date</small>
                            <p>{{ $inquiry->follow_up_date ? $inquiry->follow_up_date->format('d M Y') : '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Expected Closing</small>
                            <p>{{ $inquiry->expected_closing_date ? $inquiry->expected_closing_date->format('d M Y') : '—' }}</p>
                        </div>
                    </div>

                    @if($inquiry->notes)
                    <div class="alert alert-light border mt-2">
                        <strong>Notes:</strong><br>{{ $inquiry->notes }}
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-3 d-flex flex-wrap" style="gap:8px;">
                        @if(auth()->user()->hasRole('Administration') || $inquiry->created_by === auth()->id())
                            <a href="{{ route('inquiries.edit', $inquiry->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endif

                        {{-- Department & Admin Only Actions --}}
                        @if(!auth()->user()->hasRole('Sales') && !auth()->user()->hasRole('Engineer') || auth()->user()->hasRole('Administration'))
                            {{-- Department Review (only for early statuses) --}}
                            @if(in_array($inquiry->status, [\App\Models\Inquiry::STATUS_ASSIGNED, \App\Models\Inquiry::STATUS_UNDER_REVIEW, \App\Models\Inquiry::STATUS_NEW]))
                                <a href="{{ route('inquiries.department.create', $inquiry->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-tasks"></i> Department Review
                                </a>
                            @endif

                            {{-- Create Quotation (after department review OR after site visit completed) --}}
                            @if(in_array($inquiry->status, ['Under Review', 'Site Visit Done', 'Quotation Created']))
                                <a href="{{ route('inquiries.department.create-quotation', $inquiry->id) }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-file-invoice-dollar"></i> Create Quotation
                                </a>
                            @endif

                            {{-- Forward to Sales (after quotation created) --}}
                            @if($inquiry->status === 'Quotation Created')
                                {!! Form::open(['route' => ['inquiries.department.forward-sales', $inquiry->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa fa-paper-plane"></i> Forward to Sales', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-warning btn-sm',
                                        'onclick' => "return confirm('Forward this inquiry to Sales?')"
                                    ]) !!}
                                {!! Form::close() !!}
                            @endif
                        @endif

                        {{-- Engineer Report --}}
                        @if($inquiry->status === \App\Models\Inquiry::STATUS_SITE_VISIT_PENDING)
                            @if(auth()->user()->hasRole('Administration') || optional($inquiry->departmentReview)->visit_assigned_to === auth()->id())
                                <a href="{{ route('inquiries.engineer.create', $inquiry->id) }}" class="btn btn-dark btn-sm">
                                    <i class="fa fa-hard-hat"></i> Submit Engineer Report
                                </a>
                            @endif
                        @endif

                        {{-- Sales Follow-ups --}}
                        @if(auth()->user()->hasRole('Sales') || auth()->user()->hasRole('Administration'))
                            @if(in_array($inquiry->status, [\App\Models\Inquiry::STATUS_SENT_TO_SALES, \App\Models\Inquiry::STATUS_QUOTATION_CREATED, \App\Models\Inquiry::STATUS_UNDER_FOLLOW_UP]))
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#followUpModal">
                                    <i class="fa fa-comments"></i> Add Follow-up
                                </button>
                            @endif

                            @if(in_array($inquiry->status, [\App\Models\Inquiry::STATUS_WON, \App\Models\Inquiry::STATUS_LOST]))
                                {!! Form::open(['route' => ['inquiries.sales.close', $inquiry->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                    {!! Form::button('<i class="fa fa-lock"></i> Close Inquiry', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-dark btn-sm',
                                        'onclick' => "return confirm('Close this inquiry permanently?')"
                                    ]) !!}
                                {!! Form::close() !!}
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- ─── Department Review Card ─── --}}
            @if($inquiry->departmentReview)
                @include('inquiries.partials.department_review', ['review' => $inquiry->departmentReview])
            @endif

            {{-- ─── Engineer Report Card ─── --}}
            @if($inquiry->engineerReport)
                @include('inquiries.partials.engineer_report', ['report' => $inquiry->engineerReport])
            @endif

            {{-- ─── Sales Quotation Card ─── --}}
            @if($inquiry->quotation)
                @include('inquiries.partials.sales_quotation', ['quotation' => $inquiry->quotation])
            @endif

            {{-- ─── Follow-ups Card ─── --}}
            @if($inquiry->followUps->isNotEmpty())
                @include('inquiries.partials.follow_ups', ['followUps' => $inquiry->followUps])
            @endif

            {{-- ─── Inquiry Files ─── --}}
            @if($inquiry->getMedia('inquiry')->isNotEmpty())
            <div class="card bg-white mb-3">
                <div class="card-header"><h3 class="card-title">Inquiry Attachments</h3></div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($inquiry->getMedia('inquiry') as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ $file->getFullUrl() }}" target="_blank"><i class="fa fa-paperclip mr-1"></i>{{ $file->name }}</a>
                            <small class="text-muted">{{ $file->human_readable_size }}</small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

        </div>

        {{-- ─── Right Column: Activity Timeline ─── --}}
        <div class="col-md-4">
            @include('inquiries.partials.activity_timeline', ['activities' => $inquiry->activities])
        </div>
    </div>
</div>

{{-- ─── Follow-up Modal ─── --}}
@if(auth()->user()->hasRole('Sales') || auth()->user()->hasRole('Administration'))
<div class="modal fade" id="followUpModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Follow-up</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            {!! Form::open(['route' => ['inquiries.sales.follow-up.store', $inquiry->id], 'method' => 'POST']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('follow_up_date', 'Follow-up Date *') !!}
                    {!! Form::date('follow_up_date', now()->toDateString(), ['class' => 'form-control', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('follow_up_notes', 'Notes *') !!}
                    {!! Form::textarea('follow_up_notes', null, ['class' => 'form-control', 'rows' => 3, 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('client_feedback', 'Client Feedback') !!}
                    {!! Form::textarea('client_feedback', null, ['class' => 'form-control', 'rows' => 2]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('status', 'Status *') !!}
                    {!! Form::select('status', array_combine(\App\Models\InquiryFollowUp::STATUSES, \App\Models\InquiryFollowUp::STATUSES), 'Under Follow-up', ['class' => 'form-control', 'required' => true]) !!}
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit('Save Follow-up', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endif

@endsection
