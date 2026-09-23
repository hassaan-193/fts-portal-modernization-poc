@extends('layouts.master-no-menu')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">
                    <i class="fas fa-qrcode"></i> Attendance
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('qr-attendance.index') }}">QR Attendance</a></li>
                    <li class="breadcrumb-item active">Confirmation</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- Main Confirmation Card -->
            <div class="card card-maroon" id="confirmCard">
                <div class="card-header">
                    <h3 class="card-title" id="cardTitle">
                        <i class="fas fa-check-circle"></i> Attendance Confirmed
                    </h3>
                </div>
                <div class="card-body text-center">

                    <!-- Big status icon -->
                    <div id="statusIcon" class="mb-3">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>

                    <!-- Action badge -->
                    <div class="mb-3">
                        <span id="actionBadge" class="badge badge-lg" style="font-size: 1.2rem; padding: 10px 20px;">
                            --
                        </span>
                    </div>

                    <!-- Message -->
                    <p id="confirmMessage" class="lead text-muted mb-4">--</p>

                    <hr>

                    <!-- Details grid -->
                    <div class="row text-left mt-3">
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Employee</small>
                            <strong id="employeeName">--</strong>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Time</small>
                            <strong id="scanTime">--</strong>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Shift</small>
                            <strong id="shiftName">--</strong>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span id="lateStatus">--</span>
                        </div>
                        <div class="col-6 mb-3" id="durationRow" style="display:none;">
                            <small class="text-muted d-block">Duration</small>
                            <strong id="duration">--</strong>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Total Hours Today</small>
                            <strong id="totalHours">--</strong>
                        </div>
                    </div>

                    <hr>

                    <!-- Auto-redirect countdown -->
                    <p class="text-muted mb-3">
                        <small>Returning to scanner in <strong id="countdown">10</strong> seconds...</small>
                    </p>

                    <!-- Back button -->
                    <a href="{{ route('qr-attendance.index') }}" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-qrcode"></i> Back to Scanner
                    </a>

                </div>
            </div>

            <!-- No data fallback card (shown if user visits page directly without scan data) -->
            <div class="card card-secondary" id="noDataCard" style="display:none;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                    <h4>No Scan Data Found</h4>
                    <p class="text-muted">Please scan your QR code first.</p>
                    <a href="{{ route('qr-attendance.index') }}" class="btn btn-primary">
                        <i class="fas fa-qrcode"></i> Go to Scanner
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const raw = sessionStorage.getItem('qr_scan_result');

    if (!raw) {
        document.getElementById('confirmCard').style.display = 'none';
        document.getElementById('noDataCard').style.display = 'block';
        return;
    }

    // Clear immediately so refreshing won't re-show stale data
    sessionStorage.removeItem('qr_scan_result');

    const data = JSON.parse(raw);
    const isClockIn = data.action === 'clock_in';

    // Action badge
    const actionBadge = document.getElementById('actionBadge');
    actionBadge.textContent = isClockIn ? '⬆ Clocked In' : '⬇ Clocked Out';
    actionBadge.classList.add(isClockIn ? 'badge-success' : 'badge-primary');

    // Icon colour
    if (!isClockIn) {
        document.getElementById('statusIcon').innerHTML =
            '<i class="fas fa-sign-out-alt fa-5x text-primary"></i>';
    }

    // Card title
    document.getElementById('cardTitle').innerHTML = isClockIn
        ? '<i class="fas fa-sign-in-alt"></i> Clocked In Successfully'
        : '<i class="fas fa-sign-out-alt"></i> Clocked Out Successfully';

    // Message
    document.getElementById('confirmMessage').textContent = data.message || '';

    // Employee
    document.getElementById('employeeName').textContent = data.employee_name || '{{ Auth::user()->name ?? "" }}';

    // Time — use clock_in or clock_out depending on action
    const timeRaw = isClockIn ? data.session.clock_in : (data.session.clock_out || data.session.clock_in);
    document.getElementById('scanTime').textContent = formatUAE(timeRaw);

    // Shift
    const shiftMap = { shift_1: 'Shift 1 (7:00 AM – 1:00 PM)', shift_2: 'Shift 2 (2:00 PM – 4:00 PM)' };
    document.getElementById('shiftName').textContent = shiftMap[data.session.shift] || data.session.shift;

    // Late status
    const lateEl = document.getElementById('lateStatus');
    if (data.session.is_late) {
        lateEl.innerHTML = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Late</span>';
    } else {
        lateEl.innerHTML = '<span class="badge badge-success"><i class="fas fa-check"></i> On Time</span>';
    }

    // Duration (only meaningful on clock-out)
    if (!isClockIn && data.session.duration_minutes) {
        document.getElementById('durationRow').style.display = 'block';
        const h = Math.floor(data.session.duration_minutes / 60);
        const m = data.session.duration_minutes % 60;
        document.getElementById('duration').textContent = (h > 0 ? h + 'h ' : '') + m + 'm';
    }

    // Total hours today
    document.getElementById('totalHours').textContent = data.today_summary?.total_hours || '--';

    // Countdown auto-redirect
    let seconds = 10;
    const countdownEl = document.getElementById('countdown');
    const timer = setInterval(function () {
        seconds--;
        countdownEl.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route("qr-attendance.index") }}';
        }
    }, 1000);
});

function formatUAE(timeString) {
    if (!timeString) return '--';
    
    // Display time exactly as stored in database - no timezone conversion
    // Expected format from DB: "2026-03-24 14:35:20" or ISO format
    const match = timeString.match(/(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2}):(\d{2})/);
    if (!match) return timeString;

    const [, year, month, day, hour, minute, second] = match;
    const hours12 = parseInt(hour) % 12 || 12;
    const ampm = parseInt(hour) >= 12 ? 'PM' : 'AM';
    
    return `${month}/${day}/${year}, ${String(hours12).padStart(2, '0')}:${minute}:${second} ${ampm}`;
}</script>

<style>
.badge-lg {
    border-radius: 6px;
}
</style>
@endsection
