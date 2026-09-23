@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">
                    @lang('crud.edit') @lang('models/payment_bookings.singular')
                    <span class="badge badge-light border ml-2">{{ $paymentBooking->reference_no }}</span>
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('payment-bookings.index') !!}">@lang('models/payment_bookings.title')</a></li>
                    <li class="breadcrumb-item active">@lang('crud.edit')</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    @include('flash::message')

    @if($paymentBooking->status === \App\Models\PaymentBooking::STATUS_REJECTED)
        <div class="alert alert-warning">
            <i class="fa fa-undo mr-1"></i>
            <strong>Rejected</strong> — correct the details below and submit the booking again.
            Re-submitting clears the previous review decisions.
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">@lang('crud.edit') @lang('models/payment_bookings.singular')</h3>
                </div>
                <div class="card-body">
                    {!! Form::model($paymentBooking, ['route' => ['payment-bookings.update', $paymentBooking->id], 'method' => 'patch']) !!}
                        @include('payment_bookings.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
