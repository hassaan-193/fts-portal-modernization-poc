@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/payment_bookings.title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/payment_bookings.title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')



        {{-- Headline figures, same numbers as the dashboard --}}
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Booked this month</span>
                        <span class="info-box-number">AED {{ number_format($summary['booked_this_month'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Released this month</span>
                        <span class="info-box-number">AED {{ number_format($summary['released_this_month'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Approved, not released</span>
                        <span class="info-box-number">AED {{ number_format($summary['not_released'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-list-ol"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Entries</span>
                        <span class="info-box-number">{{ $summary['counts']['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Booking history</h3>
            </div>

            {{-- Server-side filters; the DataTable re-reads these on every draw. --}}
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="filter_booking_type">@lang('models/payment_bookings.fields.booking_type')</label>
                            <select id="filter_booking_type" class="form-control">
                                <option value="">All payment types</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="filter_status">@lang('models/payment_bookings.fields.status')</label>
                            <select id="filter_status" class="form-control">
                                <option value="all">All statuses</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="date_from">Booked from</label>
                            <input type="date" id="date_from" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="date_to">Booked to</label>
                            <input type="date" id="date_to" class="form-control">
                            <div class="mt-2">
                                <button type="button" id="filterBtn" class="btn btn-sm btn-danger btn-flat">Apply</button>
                                <button type="button" id="resetBtn" class="btn btn-sm btn-outline-secondary btn-flat">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive">
                @include('payment_bookings.table')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
    // The master layout already wires #filterBtn / #resetBtn to reload the table;
    // these two selects are the only extra state it does not know how to clear.
    $('#resetBtn').on('click', function () {
        $('#filter_booking_type').val('');
        $('#filter_status').val('all');
    });
});
</script>
@endsection
