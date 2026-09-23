@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/payment_bookings.dashboard')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('payment-bookings.index') !!}">@lang('models/payment_bookings.title')</a></li>
                        <li class="breadcrumb-item active">Release Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')
        {{-- ========== Headline figures ========== --}}
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 class="text-white" style="font-size:1.7rem;">AED {{ number_format($summary['released_this_month'], 2) }}</h3>
                        <p>Released this month</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 class="text-white" style="font-size:1.7rem;">AED {{ number_format($summary['released_next_month'], 2) }}</h3>
                        <p>Scheduled next month</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="text-white" style="font-size:1.7rem;">AED {{ number_format($summary['not_released'], 2) }}</h3>
                        <p>Approved, not yet released</p>
                    </div>
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3 class="text-white" style="font-size:1.7rem;">AED {{ number_format($summary['awaiting_review'], 2) }}</h3>
                        <p>Awaiting review</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>

        {{-- ========== Status counts ========== --}}
        <div class="row">
            <div class="col-12 col-xl-5">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header"><h3 class="card-title">By status</h3></div>
                    <div class="card-body p-0">
                        <table id="tblByStatus" class="table table-hover table-bordered table-striped table-sm text-nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('models/payment_bookings.fields.status')</th>
                                    <th class="text-right">Entries</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\PaymentBooking::statuses() as $value => $label)
                                    @php
                                        $key = [
                                            \App\Models\PaymentBooking::STATUS_DRAFT    => 'draft',
                                            \App\Models\PaymentBooking::STATUS_PENDING  => 'pending',
                                            \App\Models\PaymentBooking::STATUS_VERIFIED => 'verified',
                                            \App\Models\PaymentBooking::STATUS_APPROVED => 'approved',
                                            \App\Models\PaymentBooking::STATUS_REJECTED => 'rejected',
                                            \App\Models\PaymentBooking::STATUS_ON_HOLD  => 'on_hold',
                                        ][$value];
                                        $count = $summary['counts'][$key];
                                    @endphp
                                    <tr>
                                        <td>
                                            @include('payment_bookings.status_badge', [
                                                'booking' => new \App\Models\PaymentBooking(['status' => $value])
                                            ])
                                        </td>
                                        {{-- data-order keeps numeric sorting correct --}}
                                        <td class="text-right font-weight-bold" data-order="{{ $count }}">
                                            @if($count > 0)
                                                <a href="{{ route('payment-bookings.index', ['status' => $value]) }}">{{ $count }}</a>
                                            @else
                                                {{ $count }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th>Total entries</th>
                                    <th class="text-right">{{ $summary['counts']['total'] }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Booked this month</span>
                            <strong>AED {{ number_format($summary['booked_this_month'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted">Still to release this month</span>
                            <strong>AED {{ number_format($summary['pending_release_this_month'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== Release schedule by month ========== --}}
            <div class="col-12 col-xl-7">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">Release schedule — {{ $fromMonth && $toMonth ? 'Custom range' : 'next ' . $months . ' months' }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-default" data-toggle="collapse" data-target="#dateRangeFilter">
                                <i class="fas fa-filter"></i> Filter by date range
                            </button>
                            @if($fromMonth && $toMonth)
                                <a href="{{ route('payment-bookings.dashboard') }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-times"></i> Clear filter
                                </a>
                            @endif
                            <form method="get" class="form-inline" style="display: inline-block; margin-left: 10px;">
                                <select name="months" class="form-control form-control-sm" onchange="this.form.submit()" {{ ($fromMonth && $toMonth) ? 'disabled' : '' }}>
                                    @foreach([3, 6, 9, 12] as $option)
                                        <option value="{{ $option }}" {{ $months === $option && !($fromMonth && $toMonth) ? 'selected' : '' }}>{{ $option }} months</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- Date range filter form --}}
                    <div class="collapse {{ ($fromMonth && $toMonth) ? 'show' : '' }}" id="dateRangeFilter">
                        <div class="card-body border-bottom bg-light">
                            <form method="get" class="form-inline">
                                <label class="mr-2">From:</label>
                                <input type="month" name="from_month" class="form-control form-control-sm mr-3" value="{{ $fromMonth ?? '' }}" required>

                                <label class="mr-2">To:</label>
                                <input type="month" name="to_month" class="form-control form-control-sm mr-3" value="{{ $toMonth ?? '' }}" required>

                                <button type="submit" class="btn btn-primary btn-sm">Apply filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table id="tblSchedule" class="table table-hover table-bordered table-striped table-sm text-nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-right">Entries</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">Released</th>
                                    <th class="text-right">Not released</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedule as $bucket)
                                    {{-- data-order carries the raw value; the visible text is
                                         formatted and would otherwise sort alphabetically. --}}
                                    <tr>
                                        <td data-order="{{ $bucket['month'] }}">{{ $bucket['label'] }}</td>
                                        <td class="text-right" data-order="{{ $bucket['count'] }}">{{ $bucket['count'] }}</td>
                                        <td class="text-right" data-order="{{ $bucket['total'] }}">AED {{ number_format($bucket['total'], 2) }}</td>
                                        <td class="text-right text-success" data-order="{{ $bucket['released'] }}">AED {{ number_format($bucket['released'], 2) }}</td>
                                        <td class="text-right text-warning" data-order="{{ $bucket['not_released'] }}">AED {{ number_format($bucket['not_released'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted p-3">Nothing scheduled.</td></tr>
                                @endforelse
                            </tbody>
                            @if(count($schedule))
                            <tfoot>
                                <tr class="bg-light">
                                    <th>Total</th>
                                    <th class="text-right">{{ collect($schedule)->sum('count') }}</th>
                                    <th class="text-right">AED {{ number_format(collect($schedule)->sum('total'), 2) }}</th>
                                    <th class="text-right text-success">AED {{ number_format(collect($schedule)->sum('released'), 2) }}</th>
                                    <th class="text-right text-warning">AED {{ number_format(collect($schedule)->sum('not_released'), 2) }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br>

        {{-- ========== Upcoming releases ========== --}}
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Next approved payments due to release</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table id="tblUpcoming" class="table table-hover table-bordered table-striped table-sm text-nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('models/payment_bookings.fields.reference_no')</th>
                            <th>@lang('models/payment_bookings.fields.booking_type')</th>
                            <th>@lang('models/payment_bookings.fields.payee_project')</th>
                            <th>@lang('models/payment_bookings.fields.release_on')</th>
                            <th class="text-right">@lang('models/payment_bookings.fields.amount')</th>
                            <th>@lang('models/payment_bookings.fields.status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('payment-bookings.show', $booking->id) }}">{{ $booking->reference_no }}</a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $booking->isCash() ? 'success' : 'primary' }}">{{ $booking->type_label }}</span>
                                </td>
                                <td>
                                    {{ $booking->payee }}
                                    @if($booking->project_cost_centre)
                                        <br><small class="text-muted">{{ $booking->project_cost_centre }}</small>
                                    @endif
                                </td>
                                <td data-order="{{ $booking->effective_date ? $booking->effective_date->format('Y-m-d') : '' }}">{{ $booking->effective_date ? $booking->effective_date->format('d M Y') : '—' }}</td>
                                <td class="text-right" data-order="{{ $booking->amount }}">AED {{ number_format($booking->amount, 2) }}</td>
                                <td>@include('payment_bookings.status_badge', ['booking' => $booking])</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-3">No approved payments due in the next three months.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    {{-- datatables_js loads buttons.html5 (copy/csv/excel) but not the print
         button, which lives in its own file. Same CDN and version as the rest. --}}
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>
    <script>
    $(function () {
        // Shared config. These are small in-page tables, so they are client-side
        // rather than server-side like the index — no ajax, no paging by default.
        // Amounts and dates carry data-order so they sort by value, not by the
        // formatted string ("AED 1,850.00" would otherwise sort above "AED 900").
        // Matches the index page's DataTable config so the whole module behaves
        // the same: Bfrtip gives Buttons, the search box, the table, info and
        // pagination. Amounts and dates carry data-order so they sort by value
        // rather than by the formatted string.
        var base = {
            dom: 'Bfrtip',
            stateSave: true,
            autoWidth: false,
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-default btn-sm no-corner',
                    text: '<i class="fa fa-download"></i> {{ __('auth.app.export') }}'
                },
                {
                    extend: 'print',
                    className: 'btn btn-default btn-sm no-corner',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'
            }
        };

        $('#tblByStatus').DataTable($.extend({}, base, {
            paging: false,
            info: false,
            order: [[1, 'desc']]        // busiest status first
        }));

        $('#tblSchedule').DataTable($.extend({}, base, {
            paging: false,
            info: false,
            order: [[0, 'asc']]         // chronological
        }));

        $('#tblUpcoming').DataTable($.extend({}, base, {
            pageLength: 10,
            order: [[3, 'asc']],        // soonest release first
            columnDefs: [{ orderable: false, targets: [1, 5] }]
        }));
    });
    </script>
@endsection
