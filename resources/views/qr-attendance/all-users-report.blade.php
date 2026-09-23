@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">
                    <i class="fas fa-users-clock"></i> All Users Attendance Report
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">All Users Report</li>
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
                <div class="col-md-3">
                    <label for="userId">Select Employee:</label>
                    <select id="userId" class="form-control form-control-sm">
                        <option value="">All Employees</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="lateOnly">Filter Options:</label>
                    <select id="lateOnly" class="form-control form-control-sm">
                        <option value="">All Records</option>
                        <option value="1">Late Arrivals Only</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
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

    <!-- Report Period Card -->
    <div class="card card-maroon mb-3" id="reportPeriodCard" style="display:none;">
        <div class="card-header">
            <h3 class="card-title">Latest Scans & Activities
                <span id="filterBadge"></span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <p><strong>Period:</strong> <span id="displayStartDate"></span> to <span id="displayEndDate"></span></p>
                    <p id="activeFiltersDisplay" style="display:none;"><strong>Filters Applied:</strong> <span id="filterText"></span></p>
                </div>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered table-striped" id="activityTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Date & Time</th>
                            <th>Employee Name</th>
                            <th>Employee ID</th>
                            <th>Action</th>
                            <th>Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="activityTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- Summary Stats -->
    <div class="row mb-3" id="summaryStats" style="display:none;">
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Employees</span>
                    <span class="info-box-number" id="totalEmployees">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-sun"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Late (Shift 1)</span>
                    <span class="info-box-number" id="totalLateShift1">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-orange"><i class="fas fa-moon"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Late (Shift 2)</span>
                    <span class="info-box-number" id="totalLateShift2">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Late</span>
                    <span class="info-box-number" id="totalLate">0</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-user-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Absent</span>
                    <span class="info-box-number" id="totalAbsent">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- User Summary Table -->
    <div class="card card-maroon mb-4" id="summaryTableCard" style="display:none;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-table"></i> Employee Attendance Summary</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="summaryTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Employee Name</th>
                            <th>Employee ID</th>
                            <th>Days Worked</th>
                            <th>Late (S1)</th>
                            <th>Late (S2)</th>
                            <th>Total Late</th>
                            <th>Absent</th>
                            <th style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
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
        <i class="fas fa-inbox"></i> No attendance records found for the selected filters.
    </div>
</div>

<style>
    @media print {
        .btn, .card-header, .breadcrumb, .content-header {
            display: block !important;
        }
        .content {
            margin: 0 !important;
            padding: 0 !important;
        }
        #applyFilters {
            display: none !important;
        }
    }
</style>

@endsection

@section('scripts')
    @include('layouts.datatables_js')
    <script>
    $(document).ready(function() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const userIdSelect = document.getElementById('userId');
        const lateOnlySelect = document.getElementById('lateOnly');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const noDataMessage = document.getElementById('noDataMessage');
        const reportPeriodCard = document.getElementById('reportPeriodCard');
        const exportCard = document.getElementById('exportCard');
        const summaryStats = document.getElementById('summaryStats');
        const summaryTableCard = document.getElementById('summaryTableCard');
        const summaryTableBody = document.getElementById('summaryTableBody');
        const activityTableBody = document.getElementById('activityTableBody');

        let summaryDataTableInstance = null;
        let activityDataTableInstance = null;

        // Format time display (database stores in Dubai timezone UTC+4)
        function formatTimeInUAE(timeString) {
            if (!timeString || timeString === 'Open' || timeString === '-') return timeString;

            // Handle null or undefined
            if (timeString === null || timeString === undefined) return '-';

            // Display time exactly as stored in database - no timezone conversion
            // Expected formats: "2026-03-24 14:35:20", "2026-03-24T14:35:20+05:00", "2026-03-24 14:35:20+05:00"
            try {
                const match = timeString.toString().match(/(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2}):(\d{2})/);
                if (!match) {
                    console.warn('Time format not matched:', timeString);
                    return timeString;
                }

                const [, year, month, day, hour, minute, second] = match;
                const hourInt = parseInt(hour);
                const hours12 = hourInt % 12 || 12;
                const ampm = hourInt >= 12 ? 'PM' : 'AM';
                
                return `${month}/${day}/${year}, ${String(hours12).padStart(2, '0')}:${minute}:${second} ${ampm}`;
            } catch (e) {
                console.error('Error formatting time:', timeString, e);
                return timeString;
            }
        }

        // Initial load with default filters
        loadReportData();

        applyFiltersBtn.addEventListener('click', loadReportData);

        // Also auto-apply filters when user dropdown changes
        userIdSelect.addEventListener('change', loadReportData);
        lateOnlySelect.addEventListener('change', loadReportData);

        // Add export button click handlers
        document.getElementById('exportCsv').addEventListener('click', function(e) {
            // Get properly extracted user ID
            let userId = '';
            if (userIdSelect && userIdSelect.options && userIdSelect.selectedIndex >= 0) {
                userId = userIdSelect.options[userIdSelect.selectedIndex].value;
            }
            
            const params = new URLSearchParams({
                start_date: startDateInput.value,
                end_date: endDateInput.value,
                user_id: userId,
                late_only: lateOnlySelect.value
            });
            this.href = `{{ route('qr-attendance.export-all', ['format' => 'csv']) }}&${params.toString()}`;
        });

        document.getElementById('exportExcel').addEventListener('click', function(e) {
            // Get properly extracted user ID
            let userId = '';
            if (userIdSelect && userIdSelect.options && userIdSelect.selectedIndex >= 0) {
                userId = userIdSelect.options[userIdSelect.selectedIndex].value;
            }
            
            const params = new URLSearchParams({
                start_date: startDateInput.value,
                end_date: endDateInput.value,
                user_id: userId,
                late_only: lateOnlySelect.value
            });
            this.href = `{{ route('qr-attendance.export-all', ['format' => 'excel']) }}&${params.toString()}`;
        });

        function loadReportData() {
            // Force get value from the actual select element, bypassing any wrapper libraries
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            
            // Get the user ID from the select element directly
            let userId = '';
            if (userIdSelect && userIdSelect.options && userIdSelect.selectedIndex >= 0) {
                userId = userIdSelect.options[userIdSelect.selectedIndex].value;
            }
            
            const lateOnly = lateOnlySelect.value;

            // Show loading, hide others
            loadingSpinner.style.display = 'block';
            noDataMessage.style.display = 'none';
            reportPeriodCard.style.display = 'none';
            exportCard.style.display = 'none';
            summaryStats.style.display = 'none';
            summaryTableCard.style.display = 'none';

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                user_id: userId,  // Use the properly extracted userId
                late_only: lateOnly
            });

            const apiUrl = `{{ route('qr-attendance.api.all-users-report-data') }}?${params}`;

            // Add timeout to fetch
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

            fetch(apiUrl, { signal: controller.signal })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data && data.data.userSummaries && data.data.userSummaries.length > 0) {
                        updateUI(data.data);
                        loadingSpinner.style.display = 'none';
                        reportPeriodCard.style.display = 'block';
                        exportCard.style.display = 'block';
                        summaryStats.style.display = 'flex';
                        summaryTableCard.style.display = 'block';
                    } else {
                        loadingSpinner.style.display = 'none';
                        noDataMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    if (error.name === 'AbortError') {
                        console.error('Request timeout after 30 seconds');
                        alert('Request timeout. Please try a shorter date range.');
                    } else {
                        console.error('Error loading report:', error);
                    }
                    loadingSpinner.style.display = 'none';
                    noDataMessage.style.display = 'block';
                });
        }

        function updateUI(data) {
            try {
                // Update report period
                document.getElementById('displayStartDate').textContent = new Date(data.startDate).toLocaleDateString('en-US', { timeZone: 'Asia/Dubai', weekday: 'short', year: 'numeric', month: 'short', day: '2-digit' });
                document.getElementById('displayEndDate').textContent = new Date(data.endDate).toLocaleDateString('en-US', { timeZone: 'Asia/Dubai', weekday: 'short', year: 'numeric', month: 'short', day: '2-digit' });

                // Build filter display text
                let filterTexts = [];
                
                // Get properly extracted user ID
                let selectedUserId = '';
                if (userIdSelect && userIdSelect.options && userIdSelect.selectedIndex >= 0) {
                    selectedUserId = userIdSelect.options[userIdSelect.selectedIndex].value;
                    const selectedName = userIdSelect.options[userIdSelect.selectedIndex].text;
                    if (selectedUserId) {
                        filterTexts.push(`<i class="fas fa-user"></i> Employee: ${selectedName}`);
                    }
                }
                if (lateOnlySelect.value === '1') {
                    filterTexts.push(`<i class="fas fa-exclamation-triangle"></i> Late Arrivals Only`);
                }

                const filterDisplay = document.getElementById('activeFiltersDisplay');
                const filterText = document.getElementById('filterText');
                const filterBadge = document.getElementById('filterBadge');

                if (filterTexts.length > 0) {
                    filterText.innerHTML = filterTexts.join(' | ');
                    filterDisplay.style.display = 'block';
                    filterBadge.innerHTML = `<span class="badge badge-warning" style="margin-left: 10px;"><i class="fas fa-filter"></i> Filtered</span>`;
                } else {
                    filterDisplay.style.display = 'none';
                    filterBadge.innerHTML = '';
                }

                // Update summary stats
                document.getElementById('totalEmployees').textContent = data.summary.totalEmployees;
                document.getElementById('totalLateShift1').textContent = data.summary.totalLateShift1;
                document.getElementById('totalLateShift2').textContent = data.summary.totalLateShift2;
                document.getElementById('totalLate').textContent = data.summary.totalLate;
                document.getElementById('totalAbsent').textContent = data.summary.totalAbsent;

                // Clear and populate summary table body
                summaryTableBody.innerHTML = '';
                data.userSummaries.forEach((user, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><strong>${index + 1}</strong></td>
                        <td><strong>${user.name}</strong></td>
                        <td>${user.id}</td>
                        <td><span class="badge badge-info">${user.totalDaysWorked}</span></td>
                        <td>${user.lateDaysShift1 > 0 ? `<span class="badge badge-warning">${user.lateDaysShift1}</span>` : '<span class="badge badge-success">0</span>'}</td>
                        <td>${user.lateDaysShift2 > 0 ? `<span class="badge badge-warning">${user.lateDaysShift2}</span>` : '<span class="badge badge-success">0</span>'}</td>
                        <td>${user.lateDays > 0 ? `<span class="badge badge-danger">${user.lateDays}</span>` : '<span class="badge badge-success">0</span>'}</td>
                        <td>${user.absentDays > 0 ? `<span class="badge badge-secondary">${user.absentDays}</span>` : '<span class="badge badge-success">0</span>'}</td>
                        <td>
                            <a href="/qr-attendance/report/user/${user.id}" class="btn btn-xs btn-primary" title="View Details"><i class="fas fa-eye"></i></a>
                        </td>
                    `;
                    summaryTableBody.appendChild(row);
                });

                // Destroy existing DataTable if it exists
                if (summaryDataTableInstance) {
                    summaryDataTableInstance.destroy();
                }

                // Initialize DataTable for summary
                summaryDataTableInstance = $('#summaryTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                    "responsive": true,
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "language": {
                        "search": "Search Employee:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ employees",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });

                // Update activity table with latest scans
                updateActivityTable(data.userSummaries);

                // Update export buttons with all filter parameters
                let exportUserId = '';
                if (userIdSelect && userIdSelect.options && userIdSelect.selectedIndex >= 0) {
                    exportUserId = userIdSelect.options[userIdSelect.selectedIndex].value;
                }
                
                const baseParams = new URLSearchParams({
                    start_date: startDateInput.value,
                    end_date: endDateInput.value,
                    user_id: exportUserId,
                    late_only: lateOnlySelect.value
                });
                document.getElementById('exportCsv').href = `{{ route('qr-attendance.export-all', ['format' => 'csv']) }}&${baseParams.toString()}`;
                document.getElementById('exportExcel').href = `{{ route('qr-attendance.export-all', ['format' => 'excel']) }}&${baseParams.toString()}`;
            } catch (error) {
                console.error('Error updating UI:', error);
                loadingSpinner.style.display = 'none';
                noDataMessage.style.display = 'block';
                alert('Error loading report data. Check browser console for details.');
            }
        }

        function updateActivityTable(users) {
            try {
                // Collect all sessions from filtered users and sort by time (latest first)
                let allSessions = [];
                
                users.forEach(user => {
                    if (user.sessions && user.sessions.length > 0) {
                        user.sessions.forEach(session => {
                            // Add clock-in entry
                            allSessions.push({
                                employeeName: user.name,
                                employeeId: user.id,
                                shift: session.shift_window,
                                isLate: session.is_late,
                                action: 'Clock In',
                                actionTime: session.clock_in_time
                            });
                            
                            // Add clock-out entry (if exists)
                            if (session.clock_out_time) {
                                allSessions.push({
                                    employeeName: user.name,
                                    employeeId: user.id,
                                    shift: session.shift_window,
                                    isLate: false,
                                    action: 'Clock Out',
                                    actionTime: session.clock_out_time
                                });
                            }
                        });
                    }
                });

                console.log('Total sessions to display:', allSessions.length);

                // Sort by action time (latest first)
                allSessions.sort((a, b) => new Date(b.actionTime) - new Date(a.actionTime));

                // Clear table body
                activityTableBody.innerHTML = '';

                if (allSessions.length === 0) {
                    const row = activityTableBody.insertRow();
                    row.innerHTML = '<td colspan="7" class="text-center text-muted py-3"><i class="fas fa-inbox"></i> No activities found</td>';
                    return;
                }

                // Populate activity table with all sessions
                allSessions.forEach((session, index) => {
                    const row = activityTableBody.insertRow();
                    const timeString = formatTimeInUAE(session.actionTime);

                    const badgeClass = session.action === 'Clock Out' ? 'badge-success' : 'badge-primary';
                    const statusBadge = (session.action === 'Clock In' && session.isLate) ? '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> LATE</span>' : '<span class="badge badge-success">On Time</span>';

                    row.innerHTML = `
                        <td><strong>${index + 1}</strong></td>
                        <td><small>${timeString}</small></td>
                        <td><strong>${session.employeeName}</strong></td>
                        <td>${session.employeeId}</td>
                        <td><span class="badge ${badgeClass}"><i class="fas fa-${session.action === 'Clock Out' ? 'sign-out-alt' : 'sign-in-alt'}"></i> ${session.action}</span></td>
                        <td><small>${session.shift}</small></td>
                        <td>${statusBadge}</td>
                    `;
                });

                // Destroy existing DataTable if it exists
                if (activityDataTableInstance) {
                    activityDataTableInstance.destroy();
                }

                // Initialize DataTable for activity log
                activityDataTableInstance = $('#activityTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                    "responsive": true,
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "language": {
                        "search": "Search Activity:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ scans",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating activity table:', error);
            }
        }
    });
    </script>
@endsection
