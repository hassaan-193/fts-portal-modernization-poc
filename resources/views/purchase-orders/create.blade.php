@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Add New Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('purchase-orders.index') !!}">Purchase Order</a></li>
                    <li class="breadcrumb-item active">Add New</li>
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
                    <h3 class="card-title">Add New Purchase Order</h3>
                </div>
                <div class="card-body">
                    @if(isset($revisionSourceLpoout) && $revisionSourceLpoout)
                        <div class="alert alert-warning">
                            <i class="fa fa-code-fork"></i> Creating a <strong>revision request</strong> for LPO <strong>{{ $revisionSourceLpoout->lpo_invoice_no }}</strong> (Rev #{{ $revisionSourceLpoout->revision_number }}). Review the pre-filled data, adjust anything you need, and submit &mdash; this starts a brand-new approval workflow (Department Review &rarr; Admin Approval). The original LPO stays unchanged and available for audit until this new request is approved.
                        </div>
                    @elseif(isset($po))
                        <div class="alert alert-warning">
                            <i class="fa fa-redo"></i> Pre-filled from rejected request <strong>{{ $po->request_number }}</strong>. Review, adjust as needed, and submit &mdash; this creates a brand-new request. Attached files are not carried over and must be re-uploaded if still needed.
                        </div>
                    @endif
                    {!! Form::open(['route' => 'purchase-orders.store', 'files' => true]) !!}
                        @include('purchase-orders.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
