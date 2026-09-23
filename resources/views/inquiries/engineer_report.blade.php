@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Engineer Report — {{ $inquiry->inquiry_no }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.show', $inquiry->id) }}">{{ $inquiry->inquiry_no }}</a></li>
                    <li class="breadcrumb-item active">Engineer Report</li>
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
                    <h3 class="card-title">Submit Site Visit Report</h3>
                </div>
                <div class="card-body">
                    @include('flash::message')
                    {!! Form::open(['route' => ['inquiries.engineer.store', $inquiry->id], 'method' => 'POST', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="d-block">Visit Completed? *</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="vc_yes" name="visit_completed" value="1" class="custom-control-input"
                                        {{ optional($inquiry->engineerReport)->visit_completed ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="vc_yes">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="vc_no" name="visit_completed" value="0" class="custom-control-input"
                                        {{ (optional($inquiry->engineerReport)->visit_completed ?? null) === false ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="vc_no">No</label>
                                </div>
                                @error('visit_completed') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('site_condition_notes', 'Site Condition Notes') !!}
                                {!! Form::textarea('site_condition_notes', optional($inquiry->engineerReport)->site_condition_notes, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('scope_understanding', 'Scope Understanding') !!}
                                {!! Form::textarea('scope_understanding', optional($inquiry->engineerReport)->scope_understanding, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('materials_required', 'Materials Required') !!}
                                {!! Form::textarea('materials_required', optional($inquiry->engineerReport)->materials_required, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('challenges_risks', 'Challenges / Risks') !!}
                                {!! Form::textarea('challenges_risks', optional($inquiry->engineerReport)->challenges_risks, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('estimated_cost', 'Estimated Cost (AED)') !!}
                                {!! Form::number('estimated_cost', optional($inquiry->engineerReport)->estimated_cost, ['class' => 'form-control', 'min' => '0', 'step' => '0.01']) !!}
                                @error('estimated_cost') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('attachments[]', 'Site Visit Files') !!}
                                <input type="file" name="attachments[]" class="form-control-file" multiple>
                                <small class="text-muted">Multiple files allowed.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {!! Form::submit('Submit Report', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header"><h3 class="card-title">Site Visit Info</h3></div>
                <div class="card-body">
                    @php $review = $inquiry->departmentReview; @endphp
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>Client</th><td>{{ $inquiry->client_name }}</td></tr>
                        <tr><th>Location</th><td>{{ $inquiry->location ?? '—' }}</td></tr>
                        <tr><th>Visit Date</th><td>{{ optional(optional($review)->proposed_visit_date)->format('d M Y') ?? '—' }}</td></tr>
                        <tr><th>Visit Notes</th><td>{{ optional($review)->visit_notes ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
