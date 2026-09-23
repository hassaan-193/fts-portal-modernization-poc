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
                    <li class="breadcrumb-item active">QR Attendance</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    <div class="row">
        <!-- Scanner Card -->
        <div class="col-md-6">
            <div class="card card-maroon">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-barcode"></i> QR Scanner</h3>
                </div>
                <div class="card-body">
                    <!-- QR Scanner Video -->
                    <div id="scanner-container" class="mb-3">
                        <video id="qr-video" style="width: 100%; border: 2px solid #007bff; border-radius: 5px;"></video>
                    </div>

                    <!-- Manual Token Input -->
                    <div class="form-group">
                        <label for="qr_token">Or Enter QR Token Manually:</label>
                        <input type="text" id="qr_token" class="form-control form-control-lg" 
                               placeholder="Paste QR token here..." autocomplete="off">
                    </div>

                    <!-- Staff Info (Auto-filled from logged-in user) -->
                    <div class="form-group">
                        <label>Staff Member:</label>
                        <div class="alert alert-primary">
                            <strong>{{ Auth::user()->name ?? 'User' }}</strong>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button id="submit-scan" class="btn btn-success btn-lg btn-block" disabled>
                        <i class="fas fa-check"></i> Submit Attendance
                    </button>

                    <!-- Loader -->
                    <div id="loader" class="spinner-border text-primary d-none mt-3" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Summary Card -->
        <div class="col-md-6">
            <div class="card card-maroon">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Today's Summary</h3>
                </div>
                <div class="card-body">
                    <div id="summary-loading" class="text-center">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div id="summary-content" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Total Sessions:</small>
                                <h4 id="total-sessions">0</h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Total Hours:</small>
                                <h4 id="total-hours">0h 0m</h4>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Status:</small>
                                <h4 id="attendance-status">
                                    <span class="badge badge-secondary">--</span>
                                </h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Open Session:</small>
                                <h4 id="open-session-status">
                                    <span class="badge badge-warning">No</span>
                                </h4>
                            </div>
                        </div>

                        <hr>

                        <!-- Sessions Table -->
                        <div id="sessions-list">
                            <small class="text-muted">Sessions:</small>
                            <div id="sessions-table-container"></div>
                        </div>
                    </div>

                    <div id="summary-empty" class="text-center text-muted">
                        <p><i class="fas fa-info-circle"></i> Select a staff member to see today's attendance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Success Alert -->
<div id="success-alert" class="alert alert-success alert-dismissible fade show position-fixed" 
     style="top: 20px; right: 20px; z-index: 9999; display: none;" role="alert">
    <strong><i class="fas fa-check-circle"></i> Success!</strong>
    <p id="success-message"></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<!-- Error Alert -->
<div id="error-alert" class="alert alert-danger alert-dismissible fade show position-fixed" 
     style="top: 20px; right: 20px; z-index: 9999; display: none;" role="alert">
    <strong><i class="fas fa-times-circle"></i> Error!</strong>
    <p id="error-message"></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiToken = localStorage.getItem('api_token') || '{{ auth()->user()->api_token ?? "" }}';
    const apiUrl = '/api/v1/qr-attendance';
    
    let videoStream = null;
    let isSubmitting = false;
    let lastSubmittedQR = null;

    // Initialize QR Scanner
    async function initScanner() {
        const video = document.getElementById('qr-video');
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        let lastScannedTime = 0;
        const scanInterval = 500; // Scan every 500ms instead of continuous

        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            video.srcObject = videoStream;
            video.play();

            // Set canvas size
            video.onloadedmetadata = () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            };

            // QR scanning loop - throttled to reduce CPU usage
            function scan() {
                const now = Date.now();
                if (now - lastScannedTime > scanInterval) {
                    try {
                        ctx.drawImage(video, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, canvas.width, canvas.height);

                        if (code && !isSubmitting && code.data !== lastSubmittedQR) {
                            // Auto-submit when QR is detected
                            submitQRCode(code.data);
                        }
                        lastScannedTime = now;
                    } catch (e) {
                        console.warn('QR scan error:', e);
                    }
                }

                requestAnimationFrame(scan);
            }

            scan();
        } catch (error) {
            console.error('Camera error:', error);
            showAlert('error', 'Camera access denied. Use manual input instead.');
        }
    }

    // Submit QR code (either from auto-scan or manual button)
    function submitQRCode(qrToken) {
        if (isSubmitting) {
            return;
        }
        performSubmission(qrToken);
    }

    // Perform the actual submission
    function performSubmission(qrToken) {
        isSubmitting = true;
        lastSubmittedQR = qrToken;
        document.getElementById('loader').classList.remove('d-none');
        document.getElementById('submit-scan').disabled = true;

        fetch(`${apiUrl}/scan`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + apiToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                qr_token: qrToken,
                latitude: 0,
                longitude: 0
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('loader').classList.add('d-none');
            document.getElementById('submit-scan').disabled = false;
            isSubmitting = false;

            if (data.success) {
                // Store result and redirect to confirmation page
                data.employee_name = '{{ Auth::user()->name ?? "" }}';
                sessionStorage.setItem('qr_scan_result', JSON.stringify(data));
                window.location.href = '{{ route("qr-attendance.confirmation") }}';
            } else {
                showAlert('error', data.message);
                lastSubmittedQR = null; // Reset on error so user can retry
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Failed to process QR scan');
            document.getElementById('loader').classList.add('d-none');
            document.getElementById('submit-scan').disabled = false;
            isSubmitting = false;
            lastSubmittedQR = null; // Reset on error
        });
    }

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

    // Load today's summary
    function loadTodaysSummary() {
        document.getElementById('summary-loading').style.display = 'block';
        document.getElementById('summary-content').style.display = 'none';
        document.getElementById('summary-empty').style.display = 'none';

        fetch(`${apiUrl}/today`, {
            headers: { 'Authorization': 'Bearer ' + apiToken }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-sessions').textContent = data.total_sessions;
                document.getElementById('total-hours').textContent = data.total_hours;
                document.getElementById('attendance-status').innerHTML =
                    `<span class="badge ${data.is_absent ? 'badge-danger' : 'badge-success'}">
                        ${data.is_absent ? 'ABSENT' : 'PRESENT'}
                    </span>`;

                // Check for open session
                let hasOpenSession = data.has_open_session || false;
                let openSessionShift = data.session ? data.session.shift : '';

                if (hasOpenSession) {
                    document.getElementById('open-session-status').innerHTML =
                        `<span class="badge badge-success">Yes (${openSessionShift})</span>`;
                } else {
                    document.getElementById('open-session-status').innerHTML =
                        '<span class="badge badge-secondary">No</span>';
                }

                // Build sessions table
                let html = '<table class="table table-sm table-striped">';
                html += '<thead><tr><th>Clock In</th><th>Clock Out</th><th>Duration</th><th>Shift</th></tr></thead>';
                html += '<tbody>';

                if (data.sessions && data.sessions.length > 0) {
                    data.sessions.forEach(session => {
                        const clockInTime = formatTimeInUAE(session.clock_in);
                        const clockOutTime = session.clock_out ? formatTimeInUAE(session.clock_out) : '-';
                        html += `<tr>
                            <td>${clockInTime}</td>
                            <td>${clockOutTime}</td>
                            <td>${session.duration_minutes ? session.duration_minutes + ' min' : '-'}</td>
                            <td>
                                <span class="badge ${session.is_late ? 'badge-warning' : 'badge-info'}">
                                    ${session.shift}${session.is_late ? ' (LATE)' : ''}
                                </span>
                            </td>
                        </tr>`;
                    });
                }

                html += '</tbody></table>';
                document.getElementById('sessions-table-container').innerHTML = html;

                document.getElementById('summary-loading').style.display = 'none';
                document.getElementById('summary-content').style.display = 'block';
                document.getElementById('submit-scan').disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Failed to load summary');
            document.getElementById('summary-loading').style.display = 'none';
        });
    }

    // Manual submit button (if user wants to submit manually)
    document.getElementById('submit-scan').addEventListener('click', function() {
        const qrToken = document.getElementById('qr_token').value.trim();

        if (!qrToken) {
            showAlert('error', 'Please enter QR token');
            return;
        }

        submitQRCode(qrToken);
    });

    // Show alert
    function showAlert(type, message) {
        const alertId = type === 'success' ? 'success-alert' : 'error-alert';
        const messageId = type === 'success' ? 'success-message' : 'error-message';
        
        document.getElementById(messageId).textContent = message;
        const alert = document.getElementById(alertId);
        alert.style.display = 'block';

        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }

    // Event listeners
    // Enable submit button when user manually enters QR token
    document.getElementById('qr_token').addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            document.getElementById('submit-scan').disabled = false;
        } else {
            document.getElementById('submit-scan').disabled = true;
        }
    });

    // Initialize
    initScanner();
    loadTodaysSummary(); // Load summary on page load
});
</script>

<style>
#qr-video {
    border-radius: 5px;
    background-color: #000;
    min-height: 300px;
}

.spinner-border {
    display: inline-block;
}
</style>
@endsection
