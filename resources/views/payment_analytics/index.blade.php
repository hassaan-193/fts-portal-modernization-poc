@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
<style>
    .kpi-card { border-left: 4px solid; border-radius: 4px; }
    .kpi-card.danger  { border-color: #dc3545; }
    .kpi-card.warning { border-color: #ffc107; }
    .kpi-card.success { border-color: #28a745; }
    .kpi-card.info    { border-color: #17a2b8; }
    .kpi-card.primary { border-color: #6c4a9e; }
    .kpi-card.secondary { border-color: #6c757d; }
    .kpi-value { font-size: 1.6rem; font-weight: 700; }
    .kpi-label { font-size: 0.78rem; text-transform: uppercase; color: #888; letter-spacing: .5px; }
    .kpi-sub   { font-size: 0.82rem; color: #555; }
    .section-title { font-size: 1rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #444; border-bottom: 2px solid #eee; padding-bottom: 6px; margin-bottom: 16px; }
    .aging-bar { height: 8px; border-radius: 4px; }
    .table-sm td, .table-sm th { padding: .35rem .6rem; }
    .overdue-badge { display: inline-block; background: #dc3545; color: #fff; border-radius: 3px; padding: 1px 6px; font-size: .75rem; }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Payment Analytics</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Payment Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    {{-- ================================================================ --}}
    {{-- PIPELINE KPI ROW                                                  --}}
    {{-- ================================================================ --}}
    <div class="row">

        <div class="col-md-2">
            <div class="card kpi-card danger p-3">
                <div class="kpi-label">Overdue</div>
                <div class="kpi-value text-danger">AED {{ number_format($overdueAmount, 0) }}</div>
                <div class="kpi-sub">{{ $overdueCount }} obligation{{ $overdueCount != 1 ? 's' : '' }} past due date</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card kpi-card warning p-3">
                <div class="kpi-label">Total Pending</div>
                <div class="kpi-value text-warning">AED {{ number_format($totalPending, 0) }}</div>
                <div class="kpi-sub">{{ $pendingCount }} pending obligations</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card kpi-card info p-3">
                <div class="kpi-label">Due This Month</div>
                <div class="kpi-value text-info">AED {{ number_format($dueThisMonth, 0) }}</div>
                <div class="kpi-sub">Scheduled for {{ now()->format('M Y') }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card kpi-card primary p-3">
                <div class="kpi-label">Due Next 7 Days</div>
                <div class="kpi-value" style="color:#6c4a9e">AED {{ number_format($dueNext7, 0) }}</div>
                <div class="kpi-sub">{{ $dueNext7Count }} payment{{ $dueNext7Count != 1 ? 's' : '' }} upcoming</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card kpi-card success p-3">
                <div class="kpi-label">Paid This Month</div>
                <div class="kpi-value text-success">AED {{ number_format($paidThisMonth, 0) }}</div>
                <div class="kpi-sub">Released in {{ now()->format('M Y') }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card kpi-card secondary p-3">
                <div class="kpi-label">Paid This Year</div>
                <div class="kpi-value text-secondary">AED {{ number_format($paidThisYear, 0) }}</div>
                <div class="kpi-sub">YTD {{ now()->year }}</div>
            </div>
        </div>

    </div>

    {{-- ================================================================ --}}
    {{-- ROW 2: Monthly Execution + Aging                                  --}}
    {{-- ================================================================ --}}
    <div class="row mt-2">

        {{-- Monthly Execution --}}
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="section-title">{{ now()->format('F Y') }} Execution</div>
                <table class="table table-sm mb-3">
                    <tr>
                        <td>Planned / Due</td>
                        <td class="text-right"><strong>AED {{ number_format($plannedThisMonth, 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td class="text-right text-success"><strong>AED {{ number_format($paidInvoicesThisMonth, 0) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Remaining</td>
                        <td class="text-right text-warning"><strong>AED {{ number_format($remainingThisMonth, 0) }}</strong></td>
                    </tr>
                </table>
                <div class="d-flex justify-content-between mb-1">
                    <small>Completion</small>
                    <small><strong>{{ $completionThisMonth }}%</strong></small>
                </div>
                <div class="progress" style="height:10px">
                    <div class="progress-bar bg-success" style="width:{{ min($completionThisMonth, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Aging Analysis --}}
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="section-title">Aging Analysis (Pending)</div>
                @php $agingTotal = array_sum($aging); @endphp
                @foreach([
                    ['0–30 Days',  '0_30',  'success'],
                    ['31–60 Days', '31_60', 'warning'],
                    ['61–90 Days', '61_90', 'orange'],
                    ['90+ Days',   '90+',   'danger'],
                ] as [$label, $key, $color])
                @php
                    $amt = $aging[$key] ?? 0;
                    $cnt = $agingCounts[$key] ?? 0;
                    $pct = $agingTotal > 0 ? ($amt / $agingTotal) * 100 : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ $label }} <small class="text-muted">({{ $cnt }})</small></span>
                        <strong>AED {{ number_format($amt, 0) }}</strong>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-{{ $color === 'orange' ? 'warning' : $color }}"
                            style="width:{{ $pct }}%; {{ $color === 'orange' ? 'background-color:#fd7e14!important' : '' }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Due Next 30 Days --}}
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="section-title">Upcoming 30 Days</div>
                <div class="mb-2">
                    <span class="text-muted small">Total due</span>
                    <div style="font-size:1.4rem;font-weight:700;color:#6c4a9e">AED {{ number_format($dueNext30, 0) }}</div>
                    <div class="text-muted small">{{ $dueNext30Count }} obligation{{ $dueNext30Count != 1 ? 's' : '' }}</div>
                </div>
                @if($upcomingPayments->count())
                <div class="table-responsive mt-2" style="max-height:160px;overflow-y:auto">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light"><tr><th>Date</th><th>Vendor</th><th>AED</th></tr></thead>
                        <tbody>
                            @foreach($upcomingPayments->take(8) as $p)
                            <tr>
                                <td>{{ $p->source_date ? $p->source_date->format('d M') : '—' }}</td>
                                <td>{{ optional($p->vendor)->name ?? $p->historical_party ?? '—' }}</td>
                                <td>{{ number_format($p->total_amount, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted small mt-2">No scheduled payments in the next 30 days.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- ================================================================ --}}
    {{-- ROW 3: Vendor Liability + Overdue List                            --}}
    {{-- ================================================================ --}}
    <div class="row mt-3">

        {{-- Vendor Liability Ranking --}}
        <div class="col-md-7">
            <div class="card p-3">
                <div class="section-title">Vendor Liability Ranking <small class="text-muted">(pending obligations)</small></div>
                <div class="table-responsive">
                    <table id="tblVendorLiability" class="table table-sm table-hover" width="100%">
                        <thead >
                            <tr>
                                <th>#</th>
                                <th>Vendor</th>
                                <th>Invoices</th>
                                <th>Pending Amount</th>
                                <th>Oldest Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendorLiability as $i => $v)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><a href="{{ route('vendors.show', $v->id) }}">{{ $v->name }}</a></td>
                                <td>{{ $v->invoice_count }}</td>
                                <td><strong>AED {{ number_format($v->pending_amount, 0) }}</strong></td>
                                <td>
                                    @if($v->oldest_due)
                                        @php $dueDate = \Carbon\Carbon::parse($v->oldest_due); @endphp
                                        {{ $dueDate->format('d M Y') }}
                                        @if($dueDate->isPast())
                                            <span class="overdue-badge">{{ $dueDate->diffForHumans() }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No pending obligations.</td></tr>
                            @endforelse
                            @if($unlinkedPending > 0)
                            <tr class="table-warning">
                                <td>—</td>
                                <td><em>Unlinked payables (no vendor)</em></td>
                                <td>{{ $unlinkedCount }}</td>
                                <td>AED {{ number_format($unlinkedPending, 0) }}</td>
                                <td><a href="{{ route('vendor-payables.index') }}" class="text-warning">Fix via Vendor Payables</a></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card p-3 mb-3">
                <div class="section-title">Recently Paid</div>
                <div class="table-responsive">
                    <table id="tblRecentlyPaid" class="table table-sm" width="100%">
                        <thead class="thead-light">
                            <tr><th>Vendor</th><th>Amount</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentlyPaid as $rp)
                            <tr>
                                <td>{{ optional($rp->vendor)->name ?? $rp->historical_party ?? '—' }}</td>
                                <td>AED {{ number_format($rp->total_amount, 0) }}</td>
                                <td>{{ $rp->updated_at->format('d M') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-muted text-center">No payments recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($lposAwaitingPayment->count())
            <div class="card p-3">
                <div class="section-title">Approved LPOs — No Payment Yet</div>
                <div class="table-responsive">
                    <table id="tblLposAwaiting" class="table table-sm" width="100%">
                        <thead class="thead-light">
                            <tr><th>LPO No</th><th>Vendor</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                            @foreach($lposAwaitingPayment as $lpo)
                            <tr>
                                <td><a href="{{ route('lpoouts.show', $lpo->id) }}">{{ $lpo->lpo_invoice_no }}</a></td>
                                <td>{{ optional($lpo->vendor)->name ?? '—' }}</td>
                                <td>AED {{ number_format($lpo->total_amount, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

    </div>



    {{-- ================================================================ --}}
    {{-- ROW 5: Vendor Payment Status + Recently Paid                      --}}
    {{-- ================================================================ --}}
    <div class="row mt-3">

        {{-- Vendor Status Summary --}}
        <div class="col-md-7">
            <div class="card p-3">
                <div class="section-title">Vendor Payment Status</div>
                <div class="table-responsive">
                    <table id="tblVendorStatus" class="table table-sm table-hover" width="100%">
                        <thead >
                            <tr>
                                <th>Vendor</th>
                                <th>Pending</th>
                                <th>Paid (Total)</th>
                                <th>Next Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendorStatusSummary as $vs)
                            <tr>
                                <td><a href="{{ route('vendors.show', $vs->id) }}">{{ $vs->name }}</a></td>
                                <td>
                                    @if($vs->pending_amount > 0)
                                        <span class="text-warning font-weight-bold">AED {{ number_format($vs->pending_amount, 0) }}</span>
                                        <small class="text-muted">({{ $vs->pending_count }})</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>AED {{ number_format($vs->paid_amount, 0) }}</td>
                                <td>
                                    @if($vs->next_due)
                                        @php $nd = \Carbon\Carbon::parse($vs->next_due); @endphp
                                        {{ $nd->format('d M Y') }}
                                        @if($nd->isPast())
                                            <span class="overdue-badge">OD</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vs->pending_count === 0)
                                        <span class="badge badge-success">Clear</span>
                                    @elseif($vs->next_due && \Carbon\Carbon::parse($vs->next_due)->isPast())
                                        <span class="badge badge-danger">Overdue</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No vendor data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recently Paid + Approved LPOs Awaiting Payment --}}


    </div>

</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap.min.js"></script>
<script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(function() {
    // 'export' button in buttons.server-side.js calls dt.ajax.url() — breaks client-side tables.
    // Use only 'reload' which is safe for non-AJAX DataTables.
    var dtDefaults = {
        pageLength: 10,
        lengthMenu: [[5,10,25,50,-1],[5,10,25,50,'All']],
        stateSave: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'reload', className: 'btn btn-default btn-sm no-corner', text: '<i class="fa fa-refresh"></i> Reload' }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json' }
    };

    $('#tblVendorLiability').DataTable($.extend(true, {}, dtDefaults, {
        order: [[3, 'desc']],
        columnDefs: [{ orderable: false, targets: 0 }]
    }));

    $('#tblOverdue').DataTable($.extend(true, {}, dtDefaults, {
        order: [[2, 'desc']]
    }));

    $('#tblVendorStatus').DataTable($.extend(true, {}, dtDefaults, {
        order: [[1, 'desc']]
    }));

    $('#tblRecentlyPaid').DataTable($.extend(true, {}, dtDefaults, {
        order: [[2, 'desc']],
        pageLength: 5
    }));

    if ($('#tblLposAwaiting').length) {
        $('#tblLposAwaiting').DataTable($.extend(true, {}, dtDefaults, {
            order: [[2, 'desc']],
            pageLength: 5
        }));
    }
});

// Trend Chart
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($trendLabels) !!},
        datasets: [
            {
                label: 'Paid',
                data: {!! json_encode($trendPaid) !!},
                backgroundColor: 'rgba(40,167,69,0.75)',
                borderRadius: 3,
            },
            {
                label: 'Pending Due',
                data: {!! json_encode($trendPending) !!},
                backgroundColor: 'rgba(255,193,7,0.65)',
                borderRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'AED ' + v.toLocaleString() } }
        }
    }
});

// Payment Method Donut
@if($paymentMethods->count())
new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($paymentMethods->pluck('method')) !!},
        datasets: [{
            data: {!! json_encode($paymentMethods->pluck('total')) !!},
            backgroundColor: ['#28a745','#ffc107','#17a2b8','#dc3545','#6f42c1','#fd7e14'],
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
    }
});
@endif
</script>
@endsection
