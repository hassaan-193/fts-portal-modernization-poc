@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Create Payment Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lpoouts.index') }}">LPO Out</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lpoouts.show', $lpoout->id) }}">{{ $lpoout->lpo_invoice_no }}</a></li>
                    <li class="breadcrumb-item active">Create Payment Invoice</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@php
    $piCollection  = $lpoout->paymentInvoices ?? collect();
    $totalInvoiced = $piCollection->sum('total_amount');
    $lpoTotal      = $lpoout->total_amount ?: $lpoout->amount;
    $remaining     = max(0, $lpoTotal - $totalInvoiced);
    $invoiceCount  = $piCollection->count();
    $paidCount     = $piCollection->where('status', 1)->count();
    $pendingCount  = $invoiceCount - $paidCount;
@endphp

<div class="content">
    <div class="row">
        <div class="col-md-12">

            {{-- Order Info Dashboard --}}
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h4>{{ number_format($lpoTotal, 2) }}</h4>
                            <p>LPO Total (AED)</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-contract"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h4>{{ number_format($totalInvoiced, 2) }}</h4>
                            <p>Invoiced So Far (AED)</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h4>{{ number_format($remaining, 2) }}</h4>
                            <p>Remaining (AED)</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h4>{{ $paidCount }}</h4>
                            <p>Paid Invoices</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h4>{{ $pendingCount }}</h4>
                            <p>Pending Payment</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>

            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Create Payment Invoice — {{ $lpoout->lpo_invoice_no }}</h3>
                </div>
                <div class="card-body">

                    {{-- LPO Header Info --}}
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6">
                            <li class="callout callout-danger list-group-item mb-3 shadow">
                                <b>LPO No</b><br>{{ $lpoout->lpo_invoice_no }}
                            </li>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <li class="callout callout-danger list-group-item mb-3 shadow">
                                <b>Vendor</b><br>{{ optional($lpoout->vendor)->name ?? '—' }}
                            </li>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <li class="callout callout-danger list-group-item mb-3 shadow">
                                <b>LPO Date</b><br>{{ $lpoout->date ?? $lpoout->created_at->format('Y-m-d') }}
                            </li>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <li class="callout callout-danger list-group-item mb-3 shadow">
                                <b>Payment Type</b><br>{{ $lpoout->payment_type ?? 'N/A' }}
                            </li>
                        </div>
                    </div>

                    {{-- LPO Items --}}
                    @if($lpoout->items && count($lpoout->items) > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-danger">LPO Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Description</th>
                                            <th>Unit</th>
                                            <th>QTY</th>
                                            <th>Unit Price (AED)</th>
                                            <th>Total (AED)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lpoout->items as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item['description'] ?? 'N/A' }}</td>
                                            <td>{{ $item['unit'] ?? 'LS' }}</td>
                                            <td>{{ $item['qty'] ?? '—' }}</td>
                                            <td>{{ isset($item['unit_price']) ? number_format($item['unit_price'], 2) : '—' }}</td>
                                            <td>{{ isset($item['total']) ? number_format($item['total'], 2) : '—' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Previous Payment Invoices --}}
                    @if($piCollection->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-danger">Previous Invoices Against This LPO</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Invoice No</th>
                                            <th>Amount (AED)</th>
                                            <th>VAT (AED)</th>
                                            <th>Total (AED)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($piCollection as $pi)
                                        <tr>
                                            <td>{{ $pi->invoice_no }}</td>
                                            <td>{{ number_format($pi->amount, 2) }}</td>
                                            <td>{{ number_format($pi->vat, 2) }}</td>
                                            <td>{{ number_format($pi->total_amount, 2) }}</td>
                                            <td>
                                                @if($pi->status)
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending Payment</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    {{-- Payment Invoice Form --}}
                    {!! Form::open(['route' => 'paymentInvoices.storeFromLpoout', 'method' => 'POST']) !!}

                        <input type="hidden" name="lpoout_id" value="{{ $lpoout->id }}">
                        <input type="hidden" name="type" value="LpoOut">

                        <div class="row">
                            {{-- Invoice No --}}
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('invoice_no', 'Vendor Invoice No:') !!}
                                    {!! Form::text('invoice_no', null, [
                                        'class' => ($errors->has('invoice_no')) ? 'form-control is-invalid' : 'form-control',
                                        'placeholder' => 'Enter vendor invoice number'
                                    ]) !!}
                                    @if ($errors->has('invoice_no'))
                                        <span class="invalid-feedback"><strong>{{ $errors->first('invoice_no') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- LPO (read-only display) --}}
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label>LPO Out:</label>
                                    <input type="text" class="form-control" value="{{ $lpoout->lpo_invoice_no }} — {{ optional($lpoout->vendor)->name }}" readonly>
                                </div>
                            </div>

                            {{--
                            Invoice Date (From) / (To) — not needed on this form, disabled per request.
                            Not required server-side either, so removing them here doesn't need a backend change.
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('start_date', 'Invoice Date (From):') !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        {!! Form::text('start_date', null, [
                                            'class' => ($errors->has('start_date')) ? 'form-control is-invalid' : 'form-control',
                                            'id' => 'start_date'
                                        ]) !!}
                                        @if ($errors->has('start_date'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('start_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('end_date', 'Invoice Date (To):') !!}
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        {!! Form::text('end_date', null, [
                                            'class' => ($errors->has('end_date')) ? 'form-control is-invalid' : 'form-control',
                                            'id' => 'end_date'
                                        ]) !!}
                                        @if ($errors->has('end_date'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('end_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            --}}

                            {{-- Amount --}}
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('amount', 'Amount (AED):') !!}
                                    {!! Form::text('amount', null, [
                                        'class' => ($errors->has('amount')) ? 'form-control is-invalid' : 'form-control',
                                        'id' => 'amount',
                                        'placeholder' => '0.00'
                                    ]) !!}
                                    @if ($errors->has('amount'))
                                        <span class="invalid-feedback"><strong>{{ $errors->first('amount') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            {{--
                            Include VAT (5%) — not needed on this form, disabled per request.
                            Hidden input below keeps submissions defaulting to no-VAT; not required server-side.
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label>Include VAT (5%):</label>
                                    <div class="d-flex align-items-center mt-1">
                                        {!! Form::checkbox('vat', '1', null, ['id' => 'vat_checkbox', 'class' => 'mr-2']) !!}
                                        <small class="text-muted ml-1" id="vat_label">No VAT</small>
                                    </div>
                                </div>
                            </div>
                            --}}
                            {!! Form::hidden('vat', 0) !!}

                            {{-- Remaining Hint --}}
                            <div class="col-md-9 col-sm-6">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Remaining to invoice:</strong>
                                    <span class="font-weight-bold">{{ number_format($remaining, 2) }} AED</span>
                                    &nbsp;|&nbsp;
                                    <strong>LPO Total:</strong> {{ number_format($lpoTotal, 2) }} AED
                                    &nbsp;|&nbsp;
                                    <strong>Already Invoiced:</strong> {{ number_format($totalInvoiced, 2) }} AED
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="form-group mt-3">
                            {!! Form::label('note', 'Note:') !!}
                            {!! Form::textarea('note', null, [
                                'class' => ($errors->has('note')) ? 'form-control is-invalid' : 'form-control',
                                'rows' => 3,
                                'placeholder' => 'Additional notes for this invoice...'
                            ]) !!}
                            @if ($errors->has('note'))
                                <span class="invalid-feedback"><strong>{{ $errors->first('note') }}</strong></span>
                            @endif
                        </div>

                        <div class="form-group mt-3">
                            {!! Form::submit('Create Payment Invoice', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
                            <a href="{{ route('lpoouts.show', $lpoout->id) }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
                        </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#start_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' },
            autoUpdateInput: false,
        });
        $('#start_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('#end_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' },
            autoUpdateInput: false,
        });
        $('#end_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('#vat_checkbox').on('change', function() {
            $('#vat_label').text(this.checked ? 'VAT (5%) will be added' : 'No VAT');
        });
    });
</script>
@endsection
