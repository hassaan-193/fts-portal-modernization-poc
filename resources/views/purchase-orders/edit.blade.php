@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Edit Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('purchase-orders.index') !!}">Purchase Order</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                    <h3 class="card-title">Edit Purchase Order</h3>
                </div>
                <div class="card-body">
                    @if($po->isSentBack())
                        <div class="alert alert-warning">
                            <i class="fa fa-undo"></i> The department has <strong>sent this request back</strong> for correction.
                            Fix the points below and save &mdash; it returns to the department review queue with the same request number.
                            @if($po->sent_back_notes)
                                <hr>
                                <strong>Reason:</strong> {{ $po->sent_back_notes }}
                            @endif
                            @if($po->sent_back_at)
                                <br><small class="text-muted">
                                    Sent back {{ $po->sent_back_at->format('Y-m-d H:i') }}@if(optional($po->sentBackBy)->name) by {{ $po->sentBackBy->name }}@endif.
                                </small>
                            @endif
                        </div>
                    @elseif($po->sent_back_count)
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> This request was previously sent back
                            <strong>{{ $po->sent_back_count }}</strong> time{{ $po->sent_back_count > 1 ? 's' : '' }} by the department.
                            @if($po->sent_back_notes)
                                <br><strong>Last reason:</strong> {{ $po->sent_back_notes }}
                            @endif
                        </div>
                    @endif
                    {!! Form::model($po, ['route' => ['purchase-orders.update', $po->id], 'method' => 'patch', 'files' => true]) !!}
                        @include('purchase-orders.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
