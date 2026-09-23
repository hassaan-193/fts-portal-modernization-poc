@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/payment_bookings.approvals')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('payment-bookings.index') !!}">@lang('models/payment_bookings.title')</a></li>
                        <li class="breadcrumb-item active">Approvals</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Review queue</h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm" data-toggle="buttons">
                        <label class="btn btn-outline-secondary {{ $filter === 'pending' ? 'active' : '' }}">
                            <input type="radio" name="filter" value="pending" {{ $filter === 'pending' ? 'checked' : '' }}> Needs my action
                        </label>
                        <label class="btn btn-outline-secondary {{ $filter === 'all' ? 'active' : '' }}">
                            <input type="radio" name="filter" value="all" {{ $filter === 'all' ? 'checked' : '' }}> All in review
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                @include('payment_bookings.approvals.table')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
    // The filter is read straight off the radios by the DataTable's ajax data hook.
    $('input[name=filter]').on('change', function () {
        $('#dataTableBuilder').DataTable().ajax.reload();
    });
});
</script>
@endsection
