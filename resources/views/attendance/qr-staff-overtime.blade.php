@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
    <style>
        #qrOvertimeTable th,
        #qrOvertimeTable td {
            white-space: nowrap;
            vertical-align: middle;
        }
        #qrOvertimeTable td:last-child {
            white-space: normal;
            min-width: 220px;
        }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">QR Overtime Review</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">QR Overtime</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $summary->total ?? 0 }}</h3>
                        <p>Total QR Records</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $summary->pending ?? 0 }}</h3>
                        <p>Pending Review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $summary->approved ?? 0 }}</h3>
                        <p>Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $summary->rejected ?? 0 }}</h3>
                        <p>Rejected</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-maroon mb-3">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
            </div>
            <div class="card-body">
                <form id="qrOvertimeFilterForm" method="GET" action="{{ route('attendance.qr-overtime.index') }}">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label>Staff Name / ID</label>
                            <input type="text" name="staff_query" class="form-control form-control-sm" placeholder="e.g. John or 120" value="{{ $filters['staff_query'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label>Review Status</label>
                            <select name="review_status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="pending" {{ ($filters['review_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ ($filters['review_status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ ($filters['review_status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="qrOvertimeApplyBtn" class="btn btn-primary btn-sm mr-2" type="submit">Apply</button>
                            <button id="qrOvertimeResetBtn" class="btn btn-secondary btn-sm" type="button">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-maroon">
            <div class="card-header">
                <h3 class="card-title">Manual vs QR Overtime Comparison</h3>
            </div>
            <div class="card-body table-responsive p-2">
                <table id="qrOvertimeTable" class="table table-bordered table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Staff</th>
                            <th>Site</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours Worked</th>
                            <th>Manual OT</th>
                            <th>QR OT</th>
                            <th>Final OT</th>
                            <th>Review</th>
                            <th>Decision</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

{{-- layouts/master.blade.php uses @yield('scripts'), not @stack('scripts') --}}
@section('scripts')
@parent
@include('layouts.datatables_js')
<script>
    function toggleCustomHours(selectElement) {
        const form = selectElement.closest('form');
        if (!form) return;
        const wrapper = form.querySelector('.custom-hours-wrapper');
        const input = form.querySelector('input[name="custom_overtime_hours"]');
        const isCustom = selectElement.value === 'custom';
        if (wrapper) {
            wrapper.style.display = isCustom ? 'block' : 'none';
        }
        if (input) {
            input.required = isCustom;
            if (!isCustom) {
                input.value = '';
            }
        }
    }

    function initializeCustomHoursVisibility(rootElement) {
        rootElement.querySelectorAll('select[name="final_overtime_source"]').forEach(function (sel) {
            if (!sel.disabled) {
                toggleCustomHours(sel);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#qrOvertimeTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: false,
            order: [[0, 'desc']],
            pageLength: 25,
            ajax: {
                url: '{{ route('attendance.qr-overtime.data') }}',
                data: function (d) {
                    d.date_from = $('input[name="date_from"]').val();
                    d.date_to = $('input[name="date_to"]').val();
                    d.staff_query = $('input[name="staff_query"]').val();
                    d.review_status = $('select[name="review_status"]').val();
                }
            },
            columns: [
                { data: 'attendance_date', name: 'qsa.attendance_date', defaultContent: '-' },
                { data: 'staff', name: 'sp.name', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'site_name', name: 'site_name', defaultContent: '-' },
                { data: 'check_in_time', name: 'qsa.check_in_time', defaultContent: '-' },
                { data: 'check_out_time', name: 'qsa.check_out_time', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'hours_worked_col', name: 'qsa.duration_minutes', orderable: false, searchable: false, defaultContent: '0h' },
                { data: 'manual_hours', name: 'manual_overtime_hours', orderable: false, searchable: false, defaultContent: '0h' },
                { data: 'qr_hours', name: 'qsa.overtime_minutes', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'final_hours', name: 'qsa.final_overtime_minutes', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'review_badge', name: 'qsa.review_status', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'decision_form', name: 'decision_form', orderable: false, searchable: false, defaultContent: '-' }
            ],
            drawCallback: function () {
                initializeCustomHoursVisibility(document);
            }
        });

        $('#qrOvertimeFilterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#qrOvertimeResetBtn').on('click', function () {
            const form = document.getElementById('qrOvertimeFilterForm');
            form.reset();
            table.ajax.reload();
        });
    });
</script>
@endsection

