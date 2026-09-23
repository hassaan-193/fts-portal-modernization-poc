@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Approve & Email Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('invoices.request_invoices') !!}">Invoice Requests</a></li>
                    <li class="breadcrumb-item active">Approve & Email</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Approve & Email Invoice</h3>
                </div>
                <div class="card-body">
                    @include('flash::message')

                    <form method="POST" action="{{ route('invoices.approve-and-email') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="invoice_request_id" value="{{ $invoiceRequest->id }}">

                        <div class="row">
                            <!-- Letterhead Selection -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="letterhead_type">Select Invoice Template <span class="text-danger">*</span></label>
                                    <select name="letterhead_type" id="letterhead_type" class="form-control @error('letterhead_type') is-invalid @enderror" required>
                                        <option value="">-- Select Template --</option>
                                        <option value="fts">FTS (Fire Technical Services)</option>
                                        <option value="experts">EXPERTS (EIC)</option>
                                        <option value="ftsits">FTSITS</option>
                                    </select>
                                    @error('letterhead_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Company Email (Display Only) -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Company Email (Recipient)</label>
                                    <input type="text" class="form-control" readonly value="{{ $invoiceRequest->requestable->company->email ?? $invoiceRequest->requestable->company->billing_email ?? 'No email found' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <!-- Fixed CC Emails (Checkboxes) -->
                                <div class="form-group">
                                    <label>Default CC Recipients</label>
                                    <div class="form-check">
                                        @foreach(config('invoices.fixed_cc_emails', []) as $key => $email)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="fixed_cc_emails[]" id="fixed_cc_{{ $key }}" value="{{ $email }}">
                                                <label class="form-check-label" for="fixed_cc_{{ $key }}">
                                                    {{ ucfirst($key) }} - {{ $email }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <!-- CC Email Addresses -->
                                <div class="form-group">
                                    <label for="cc_emails">Other CC</label>
                                    <textarea name="cc_emails" id="cc_emails" class="form-control @error('cc_emails') is-invalid @enderror" rows="3"></textarea>
                                    @error('cc_emails')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Enter multiple email addresses separated by commas.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <!-- File Attachments -->
                        <div class="form-group">
                            <label for="attachments">Attach Additional Files (Optional)</label>
                            <input type="file" class="form-control-file @error('attachments') is-invalid @enderror" name="attachments[]" id="attachments" multiple accept="*/*">
                            @error('attachments')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @error('attachments.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                You can upload multiple files. Max 10MB per file.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <a href="{{ route('invoices.request_invoices') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
                                    <button type="submit" class="btn btn-danger btn-flat btn-lg">
                                        <i class="fas fa-check"></i> Approve & Send Email
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
