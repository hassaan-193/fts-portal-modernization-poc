@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Create Quotation — {{ $inquiry->inquiry_no }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.show', $inquiry->id) }}">{{ $inquiry->inquiry_no }}</a></li>
                    <li class="breadcrumb-item active">Quotation</li>
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
                    <h3 class="card-title">Quotation Details</h3>
                </div>
                <div class="card-body">
                    @include('flash::message')
                    {!! Form::open(['route' => ['inquiries.department.quotation.store', $inquiry->id], 'method' => 'POST', 'files' => true]) !!}

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('quotation_amount', 'Quotation Amount (AED) *') !!}
                                {!! Form::number('quotation_amount', null, ['class' => $errors->has('quotation_amount') ? 'form-control is-invalid' : 'form-control', 'min' => '0', 'step' => '0.01', 'required' => true]) !!}
                                @error('quotation_amount') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('validity_date', 'Validity Date') !!}
                                {!! Form::date('validity_date', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('quotation_file', 'Quotation File (PDF)') !!}
                                <input type="file" name="quotation_file" class="form-control-file" accept=".pdf,.doc,.docx">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('scope_of_work', 'Scope of Work') !!}
                                {!! Form::textarea('scope_of_work', null, ['class' => 'form-control', 'rows' => 4]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('terms_conditions', 'Terms & Conditions') !!}
                                {!! Form::textarea('terms_conditions', null, ['class' => 'form-control', 'rows' => 4]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {!! Form::submit('Create Quotation', ['class' => 'btn btn-success']) !!}
                        <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header"><h3 class="card-title">Inquiry Summary</h3></div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>Client</th><td>{{ $inquiry->client_name }}</td></tr>
                        <tr><th>Type</th><td>{{ $inquiry->inquiry_type }}</td></tr>
                        <tr><th>Location</th><td>{{ $inquiry->location ?? '—' }}</td></tr>
                        <tr><th>Expected Price</th><td>{{ $inquiry->expected_price ? 'AED '.number_format($inquiry->expected_price,2) : '—' }}</td></tr>
                    </table>
                    @if($inquiry->engineerReport)
                    <hr>
                    <p class="text-muted small mb-1">Engineer Estimated Cost:</p>
                    <strong class="text-success">AED {{ number_format($inquiry->engineerReport->estimated_cost ?? 0, 2) }}</strong>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
