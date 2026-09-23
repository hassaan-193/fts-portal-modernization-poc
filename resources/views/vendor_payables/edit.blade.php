@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Edit Vendor Payable</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('vendor-payables.index') }}">Vendor Payables</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendor-payables.show', $payable->id) }}">#{{ $payable->id }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Edit Payable #{{ $payable->id }}</h3>
                </div>
                <div class="card-body">

                    <form action="{{ route('vendor-payables.update', $payable->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row">

                            {{-- Vendor --}}
                            <div class="form-group col-md-6">
                                <label>Vendor <span class="text-danger">*</span></label>
                                <select name="vendor_id" id="vendor_id"
                                    class="form-control select2 @error('vendor_id') is-invalid @enderror">
                                    <option value="">— Select Vendor —</option>
                                    @foreach($vendors as $id => $name)
                                        <option value="{{ $id }}" {{ (old('vendor_id', $payable->vendor_id) == $id) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Project --}}
                            <div class="form-group col-md-6">
                                <label>Project <span class="text-muted"></span></label>
                                <select name="project_id" id="project_id"
                                    class="form-control select2 @error('project_id') is-invalid @enderror">
                                    <option value="">— None —</option>
                                    @foreach($projects as $id => $subject)
                                        <option value="{{ $id }}" {{ (old('project_id', $payable->project_id) == $id) ? 'selected' : '' }}>{{ $subject }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Amount --}}
                            <div class="form-group col-md-4">
                                <label>Amount (AED) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" step="0.01" min="0.01"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    value="{{ old('amount', $payable->total_amount) }}" placeholder="0.00">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Due Date --}}
                            <div class="form-group col-md-4">
                                <label>Due / Expected Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <input type="text" name="due_date" id="due_date"
                                        class="form-control @error('due_date') is-invalid @enderror"
                                        value="{{ old('due_date', $payable->source_date ? $payable->source_date->format('Y-m-d') : '') }}"
                                        placeholder="YYYY-MM-DD" autocomplete="off">
                                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Reference No --}}
                            <div class="form-group col-md-4">
                                <label>Reference / Cheque No</label>
                                <input type="text" name="reference_no"
                                    class="form-control @error('reference_no') is-invalid @enderror"
                                    value="{{ old('reference_no', $payable->invoice_no) }}">
                                @error('reference_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Source Reference --}}
                            <div class="form-group col-md-12">
                                <label>Source / Document Reference</label>
                                <input type="text" name="source_reference"
                                    class="form-control @error('source_reference') is-invalid @enderror"
                                    value="{{ old('source_reference', $payable->source_reference) }}">
                                @error('source_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Notes --}}
                            <div class="form-group col-md-12">
                                <label>Notes</label>
                                <textarea name="note" rows="3"
                                    class="form-control @error('note') is-invalid @enderror">{{ old('note', $payable->note) }}</textarea>
                                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Supporting Document --}}
                            <div class="form-group col-md-12">
                                <label>Supporting Document <span class="text-muted"></span></label>
                                @if($payable->document_path)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($payable->document_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fa fa-file"></i> View Current Document
                                    </a>
                                    <small class="text-muted ml-2">Uploading a new file will replace this one.</small>
                                </div>
                                @endif
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-paperclip"></i></span>
                                    </div>
                                    <input type="file" name="document" class="form-control @error('document') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-group col-12 mt-2">
                                <button type="submit" class="btn btn-danger btn-lg btn-flat">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <a href="{{ route('vendor-payables.show', $payable->id) }}" class="btn btn-outline-secondary btn-lg btn-flat ml-2">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
    $('.select2').select2({ theme: 'bootstrap4', placeholder: 'Select an option', allowClear: true });
    $('#due_date').daterangepicker({
        singleDatePicker: true,
        locale: { format: 'YYYY-MM-DD' },
        autoApply: true
    });
</script>
@endsection
