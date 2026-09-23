@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">
                    <i class="fas fa-user-clock"></i> User Attendance Report
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">User Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    <!-- Filters Card -->
    <div class="card card-maroon mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Report Filters</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <label for="startDate">From Date:</label>
                    <input type="date" id="startDate" class="form-control form-control-sm" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="endDate">To Date:</label>
                    <input type="date" id="endDate" class="form-control form-control-sm" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="userId">Select Employee:</label>
                    <select id="userId" class="form-control form-control-sm">
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}" {{ $u->id == $userId ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="applyFilters" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Export Buttons -->
    <div class="card card-secondary mb-3" id="exportCard" style="display:none;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-download"></i> Export Report</h3>
        </div>
        <div class="card-body">
            <a href="#" id="exportCsv" class="btn btn-sm btn-success">
                <i class="fas fa-file-csv"></i> Export to CSV
            </a>
            <a href="#" id="exportExcel" class="btn btn-sm btn-info">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
            <button onclick="window.print();" class="btn btn-sm btn-warning">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="card card-maroon mb-3" id="userInfoCard" style="display:none;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> Employee Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <span id="employeeName"></span></p>
                    <p><strong>Employee ID:</strong> <span id="employeeId"></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Report Period:</strong> <span id="reportPeriod"></span></p>
                    <p><strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-3" id="summaryStats" style="display:none;">
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Days Worked</span>
                    <span class="info-box-number" id="totalDays">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-sun"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Late (Shift 1)</span>
                    <span class="info-box-number" id="lateDaysShift1">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-orange"><i class="fas fa-moon"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Late (Shift 2)</span>
                    <span class="info-box-number" id="lateDaysShift2">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Late</span>
                    <span class="info-box-number" id="lateDays">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Absent Days</span>
                    <span class="info-box-number" id="absentDays">0</span>
                </div>
            </div>
        </div>
    </div>



    <!-- Daily Summary Table -->
    <div class="card card-maroon mb-4" id="tableCard" style="display:none;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-table"></i> Daily Attendance Summary</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Clock In (First)</th>
                            <th>Clock Out (Last)</th>
                            <th>Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-hourglass-start"></i> Loading data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2">Loading report data...</p>
    </div>

    <!-- No Data Message -->
    <div id="noDataMessage" class="alert alert-info text-center" style="display:none;">
        <i class="fas fa-inbox"></i> No attendance records found for the selected period.
    </div>
</div>

<style>
    @media print {
        .btn, .card-header, .breadcrumb, .content-header, #applyFilters {
            display: block !important;
        }
        .content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .form-control {
            display: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const userIdSelect = document.getElementById('userId');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noDataMessage = document.getElementById('noDataMessage');
    const userInfoCard = document.getElementById('userInfoCard');
    const summaryStats = document.getElementById('summaryStats');
    const tableCard = document.getElementById('tableCard');
    const tableBody = document.getElementById('tableBody');
    const exportCard = document.getElementById('exportCard');

    // Format time in UAE timezone
    function formatTimeInUAE(timeString) {
        if (!timeString || timeString === 'Open' || timeString === '-') return timeString;

        // Display time exactly as stored in database - no timezone conversion
        // Expected format from DB: "2026-03-24 14:35:20" or ISO format
        const match = timeString.match(/(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2}):(\d{2})/);
        if (!match) return timeString;

        const [, year, month, day, hour, minute, second] = match;
        const hours12 = parseInt(hour) % 12 || 12;
        const ampm = parseInt(hour) >= 12 ? 'PM' : 'AM';
        
        return `${month}/${day}/${year}, ${String(hours12).padStart(2, '0')}:${minute}:${second} ${ampm}`;
    }

    // Initial load with default filters
    loadReportData();

    applyFiltersBtn.addEventListener('click', loadReportData);

    function loadReportData() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const userId = userIdSelect.value;

        // Show loading, hide others
        loadingSpinner.style.display = 'block';
        noDataMessage.style.display = 'none';
        userInfoCard.style.display = 'none';
        summaryStats.style.display = 'none';
        tableCard.style.display = 'none';
        exportCard.style.display = 'none';

        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate
        });

        fetch(`{{ route('qr-attendance.api.user-report-data', ['userId' => ':userId']) }}`.replace(':userId', userId) + `?${params}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success && data.data.dailyRecords.length > 0) {
                    updateUI(data.data);
                    loadingSpinner.style.display = 'none';
                    userInfoCard.style.display = 'block';
                    summaryStats.style.display = 'flex';
                    tableCard.style.display = 'block';
                    exportCard.style.display = 'block';
                } else {
                    loadingSpinner.style.display = 'none';
                    noDataMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading report:', error);
                loadingSpinner.style.display = 'none';
                noDataMessage.style.display = 'block';
            });
    }

    function updateUI(data) {
        // Update user info
        document.getElementById('employeeName').textContent = data.user.name;
        document.getElementById('employeeId').textContent = data.user.id;
        document.getElementById('reportPeriod').textContent =
            new Date(data.startDate).toLocaleDateString('en-US', { timeZone: 'Asia/Dubai' }) + ' to ' +
            new Date(data.endDate).toLocaleDateString('en-US', { timeZone: 'Asia/Dubai' });

        // Update summary stats
        document.getElementById('totalDays').textContent = data.summary.totalDays;
        document.getElementById('lateDaysShift1').textContent = data.summary.lateDaysShift1;
        document.getElementById('lateDaysShift2').textContent = data.summary.lateDaysShift2;
        document.getElementById('lateDays').textContent = data.summary.lateDays;
        document.getElementById('absentDays').textContent = data.summary.absentDays;

        // Update table
        tableBody.innerHTML = '';
        data.dailyRecords.forEach(day => {
            const clockInTime = formatTimeInUAE(day.clockInTime);
            const clockOutTime = formatTimeInUAE(day.clockOutTime);

            let statusBadge = '';
            if (day.isLateShift1 && day.isLateShift2) {
                statusBadge = '<span class="badge badge-danger">Late S1 &amp; S2</span>';
            } else if (day.isLateShift1) {
                statusBadge = '<span class="badge badge-warning">Late (S1)</span>';
            } else if (day.isLateShift2) {
                statusBadge = '<span class="badge badge-orange">Late (S2)</span>';
            } else {
                statusBadge = '<span class="badge badge-success">On Time</span>';
            }

            const row = `
                <tr>
                    <td><strong>${day.date}</strong></td>
                    <td>${day.dayName}</td>
                    <td><span class="badge badge-primary">${clockInTime}</span></td>
                    <td><span class="badge badge-secondary">${clockOutTime}</span></td>
                    <td><span class="badge badge-info">${day.shiftWindow.toUpperCase().replace('SHIFT_', '')}</span></td>
                    <td>${statusBadge}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        // Update export buttons
        const baseParams = `start_date=${startDateInput.value}&end_date=${endDateInput.value}`;
        const userId = userIdSelect.value;
        document.getElementById('exportCsv').href = `{{ route('qr-attendance.export-user', ['userId' => ':userId', 'format' => 'csv']) }}`.replace(':userId', userId) + `?${baseParams}`;
        document.getElementById('exportExcel').href = `{{ route('qr-attendance.export-user', ['userId' => ':userId', 'format' => 'excel']) }}`.replace(':userId', userId) + `?${baseParams}`;
    }
});
</script>
@endsection
