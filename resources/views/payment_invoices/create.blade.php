@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">@lang('crud.add_new') @lang('models/payment_invoices.singular')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('paymentInvoices.index') !!}">@lang('models/payment_invoices.singular')</a></li>
                    <li class="breadcrumb-item active">@lang('crud.add_new')</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">

            {{-- LPO Financial Summary (shown when coming from an invoice request) --}}
            @if(isset($lpoout) && $lpoout)
            @php
                $piCollection  = $lpoout->paymentInvoices ?? collect();
                $totalInvoiced = $piCollection->sum('total_amount');
                $totalPaid     = $piCollection->where('status', 1)->sum('total_amount');
                $outstanding   = $totalInvoiced - $totalPaid;
                $lpoTotal      = $lpoout->total_amount ?: $lpoout->amount;
                $remaining     = max(0, $lpoTotal - $totalInvoiced);
            @endphp
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

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h4>{{ number_format($remaining, 2) }}</h4>
                            <p>Remaining to Invoice (AED)</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
            </div>

            {{-- Request Note --}}
            @if(isset($invoiceRequest) && $invoiceRequest->note)
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle"></i> Request Note:</strong>
                {{ $invoiceRequest->note }}
                @if($invoiceRequest->delivery_date)
                    &nbsp;|&nbsp; <strong>Delivery Date:</strong> {{ $invoiceRequest->delivery_date }}
                @endif
            </div>
            @endif
            @endif

            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">@lang('crud.add_new') @lang('models/payment_invoices.singular')</h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'paymentInvoices.store']) !!}
                        @include(strtolower(__('models/payment_invoices.plural')).'.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

