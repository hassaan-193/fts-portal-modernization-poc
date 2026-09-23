@extends('pwa.app')

@section('styles')
<style>
    .scanner-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: #f8f9fa;
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .header button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 4px 8px;
    }

    .header-content {
        flex: 1;
        text-align: center;
    }

    .header-content h1 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .header-content p {
        font-size: 11px;
        margin: 2px 0 0 0;
        opacity: 0.9;
    }

    .content-area {
        flex: 1;
        overflow-y: auto;
        padding: 12px 16px;
        padding-bottom: 20px;
    }

    .site-section {
        margin-bottom: 12px;
    }

    .site-section label {
        display: block;
        font-size: 13px;
        color: #FF6B5B;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .site-section select,
    .site-section input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        background: white;
    }

    .site-section select:focus,
    .site-section input:focus {
        outline: none;
        border-color: #FF6B5B;
        box-shadow: 0 0 0 2px rgba(255, 107, 91, 0.15);
    }

    .scanner-box {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 12px;
        border: 1px solid #e5e7eb;
    }

    .scanner-box video {
        width: 100%;
        display: block;
        background: #000;
        min-height: 220px;
        max-height: 280px;
        object-fit: cover;
    }

    .scanner-label {
        text-align: center;
        padding: 8px;
        font-size: 12px;
        color: #999;
    }

    .scanner-label.active {
        color: #FF6B5B;
        font-weight: 600;
    }

    .manual-entry {
        background: white;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .manual-entry label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
    }

    .manual-entry .row {
        display: flex;
        gap: 8px;
    }

    .manual-entry input {
        flex: 1;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
    }

    .manual-entry button {
        border: none;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 13px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    }

    .manual-entry button:disabled {
        opacity: 0.6;
    }

    .scan-status {
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 12px;
        font-size: 12px;
        border-left: 4px solid;
    }

    .scan-status.info {
        background: #FFF5F3;
        color: #9f3a2e;
        border-left-color: #FF6B5B;
    }

    .scan-status.success {
        background: #e7f7ed;
        color: #155724;
        border-left-color: #28a745;
    }

    .scan-status.error {
        background: #feecec;
        color: #b91c1c;
        border-left-color: #dc3545;
    }

    .alert-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .alert-card {
        background: white;
        border-radius: 16px;
        padding: 28px 24px;
        text-align: center;
        max-width: 320px;
        width: 100%;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .alert-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .alert-card h3 {
        font-size: 18px;
        margin: 0 0 6px 0;
        color: #2c3e50;
    }

    .alert-card p {
        font-size: 13px;
        color: #666;
        margin: 0 0 4px 0;
    }

    .alert-card .time-info {
        font-size: 12px;
        color: #999;
        margin: 8px 0 16px 0;
    }

    .alert-card .overtime-badge {
        display: inline-block;
        background: #fff3cd;
        color: #856404;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin: 8px 0;
    }

    .alert-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 4px;
    }

    .alert-btn-primary {
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
    }

    .alert-btn-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
    }

    .alert-btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #333;
    }

    .alert-btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .records-section h4 {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 8px 0;
    }

    .summary-row {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .summary-card {
        flex: 1;
        background: white;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .summary-card .num {
        font-size: 22px;
        font-weight: 700;
        color: #FF6B5B;
    }

    .summary-card .label {
        font-size: 10px;
        color: #999;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .record-card {
        background: white;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .record-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .record-avatar.in {
        background: linear-gradient(135deg, #28a745, #218838);
    }

    .record-avatar.out {
        background: linear-gradient(135deg, #6c757d, #555);
    }

    .record-info {
        flex: 1;
        min-width: 0;
    }

    .record-name {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .record-time {
        font-size: 11px;
        color: #999;
        margin: 2px 0 0 0;
    }

    .record-status {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .record-status.in {
        background: #d4edda;
        color: #155724;
    }

    .record-status.out {
        background: #e2e3e5;
        color: #383d41;
    }

    .no-records {
        text-align: center;
        padding: 20px;
        color: #999;
        font-size: 13px;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #dc3545;
        color: #c33;
        padding: 10px 14px;
        margin-bottom: 12px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    .error-alert button {
        background: none;
        border: none;
        color: #c33;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        gap: 12px;
    }

    .spinner {
        width: 32px;
        height: 32px;
        border: 3px solid #f0f0f0;
        border-top-color: #FF6B5B;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .tab-bar {
        display: flex;
        background: white;
        border-radius: 10px;
        padding: 3px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .tab-btn {
        flex: 1;
        padding: 8px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: transparent;
        color: #999;
        transition: all 0.2s;
    }

    .tab-btn.active {
        background: #FF6B5B;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="scanner-container" x-data="qrScannerApp()">

    <div class="header">
        <button onclick="window.location.href='/pwa/home'">‹</button>
        <div class="header-content">
            <h1>QR Staff Attendance</h1>
            <p>Scan staff QR codes</p>
        </div>
        <button @click="if(confirm('Logout?')) { localStorage.removeItem('foreman_token'); window.location.href='/pwa/login'; }" style="color: white; font-size: 20px;">➜]</button>
    </div>

    <div class="content-area">

        <!-- Site Selection -->
        <div class="site-section">
            <label>Select Site</label>
            <select x-model="selectedSite" @change="isOtherSite = (selectedSite === 'other'); if (!isOtherSite) customSiteName = '';">
                <option value="" disabled selected>Choose a site...</option>
                <template x-for="site in sites" :key="site.id">
                    <option :value="site.id" x-text="site.site_name"></option>
                </template>
                <option value="other">Other (Custom)</option>
            </select>
        </div>

        <template x-if="isOtherSite">
            <div class="site-section">
                <label>Custom Site Name</label>
                <input type="text" x-model="customSiteName" placeholder="Enter site name...">
            </div>
        </template>

        <!-- Error -->
        <template x-if="error">
            <div class="error-alert">
                <span x-text="error"></span>
                <button @click="error = ''" type="button">×</button>
            </div>
        </template>

        <!-- Tab Bar -->
        <div class="tab-bar">
            <button :class="'tab-btn' + (activeTab === 'scanner' ? ' active' : '')" @click="activeTab = 'scanner'">Scanner</button>
            <button :class="'tab-btn' + (activeTab === 'records' ? ' active' : '')" @click="activeTab = 'records'; loadTodayRecords();">Today's Records</button>
        </div>

        <!-- Scanner Tab -->
        <template x-if="activeTab === 'scanner'">
            <div>
                <template x-if="!siteSelected()">
                    <div class="no-records">
                        Please select a site before scanning.
                    </div>
                </template>

                <template x-if="siteSelected()">
                    <div>
                        <div class="scan-status" :class="statusType" x-text="statusMessage"></div>

                        <div class="scanner-box">
                            <video id="qr-video" autoplay playsinline></video>
                            <div :class="'scanner-label' + (scanning ? ' active' : '')">
                                <span x-show="scanning">Scanning... Point camera at QR code</span>
                                <span x-show="!scanning">Camera loading...</span>
                            </div>
                        </div>

                        <!-- <div class="manual-entry">
                            <label>Or enter QR token manually</label>
                            <div class="row">
                                <input type="text" x-model="manualQrToken" placeholder="Paste QR token here...">
                                <button @click="submitManualToken()" :disabled="processing || !manualQrToken.trim()">Submit</button>
                            </div>
                        </div> -->

                        <template x-if="processing">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                                <p style="font-size: 12px; color: #999;">Processing scan...</p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <!-- Records Tab -->
        <template x-if="activeTab === 'records'">
            <div class="records-section">
                <div class="summary-row">
                    <div class="summary-card">
                        <div class="num" x-text="summary.total">0</div>
                        <div class="label">Total</div>
                    </div>
                    <div class="summary-card">
                        <div class="num" style="color: #28a745;" x-text="summary.checked_in">0</div>
                        <div class="label">Checked In</div>
                    </div>
                    <div class="summary-card">
                        <div class="num" style="color: #6c757d;" x-text="summary.checked_out">0</div>
                        <div class="label">Checked Out</div>
                    </div>
                </div>

                <h4>Today's Scans</h4>

                <template x-if="loadingRecords">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p style="font-size: 12px; color: #999;">Loading records...</p>
                    </div>
                </template>

                <template x-if="!loadingRecords && records.length === 0">
                    <div class="no-records">No attendance records for today yet.</div>
                </template>

                <template x-if="!loadingRecords">
                    <div>
                        <template x-for="rec in records" :key="rec.id">
                            <div class="record-card">
                                <div :class="'record-avatar ' + (rec.status === 'checked_in' ? 'in' : 'out')">
                                    <span x-text="rec.staff_name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="record-info">
                                    <div class="record-name" x-text="rec.staff_name"></div>
                                    <div class="record-time">
                                        <span x-text="'In: ' + rec.check_in_time"></span>
                                        <template x-if="rec.check_out_time">
                                            <span x-text="' | Out: ' + rec.check_out_time"></span>
                                        </template>
                                        <template x-if="rec.hours_worked">
                                            <span x-text="' | Hours: ' + rec.hours_worked"></span>
                                        </template>
                                        <template x-if="rec.overtime_minutes > 0">
                                            <span style="color: #856404;" x-text="' | OT: ' + (rec.overtime_display || rec.overtime_minutes + 'm')"></span>
                                        </template>
                                    </div>
                                </div>
                                <div :class="'record-status ' + (rec.status === 'checked_in' ? 'in' : 'out')"
                                     x-text="rec.status === 'checked_in' ? 'IN' : 'OUT'"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- Alert Overlay -->
    <template x-if="showAlert">
        <div class="alert-overlay" @click.self="dismissAlert()">
            <div class="alert-card">
                <div class="alert-icon" x-text="alertData.icon"></div>
                <h3 x-text="alertData.title"></h3>
                <p x-text="alertData.staffName"></p>
                <div class="time-info" x-html="alertData.timeInfo"></div>
                <template x-if="alertData.overtimeMinutes > 0">
                    <div class="overtime-badge" x-text="'Overtime: ' + alertData.overtimeMinutes + ' min'"></div>
                </template>
                <button :class="'alert-btn ' + alertData.btnClass" @click="dismissAlert()" x-text="alertData.btnText"></button>
            </div>
        </div>
    </template>

</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
function qrScannerApp() {
    return {
        token: localStorage.getItem('foreman_token'),
        sites: [],
        selectedSite: '',
        customSiteName: '',
        isOtherSite: false,
        activeTab: 'scanner',
        scanning: false,
        processing: false,
        error: '',
        records: [],
        summary: { total: 0, checked_in: 0, checked_out: 0 },
        loadingRecords: false,
        showAlert: false,
        alertData: {},
        videoStream: null,
        scannerActive: false,
        lastScannedId: null,
        lastScanTime: 0,
        manualQrToken: '',
        statusMessage: 'Waiting for QR scan...',
        statusType: 'info',
        lastInvalidQr: null,
        lastInvalidQrAt: 0,

        init() {
            if (!this.token) {
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.loadSites();

            this.$watch('activeTab', (val) => {
                if (val === 'scanner' && this.siteSelected()) {
                    this.$nextTick(() => this.startScanner());
                } else {
                    this.stopScanner();
                }
            });

            this.$watch('selectedSite', () => {
                if (this.siteSelected() && this.activeTab === 'scanner') {
                    this.$nextTick(() => this.startScanner());
                }
            });
        },

        siteSelected() {
            if (this.selectedSite === 'other') {
                return this.customSiteName.trim().length > 0;
            }
            return this.selectedSite !== '';
        },

        async loadSites() {
            try {
                const res = await axios.get('/api/v1/pwa-qr-attendance/sites');
                if (res.data.success) {
                    this.sites = res.data.data;
                }
            } catch (e) {
                console.error('Failed to load sites:', e);
                this.error = 'Failed to load sites.';
            }
        },

        async startScanner() {
            if (this.scannerActive) return;

            const video = document.getElementById('qr-video');
            if (!video) return;

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            let lastScannedTime = 0;
            const scanInterval = 500;

            try {
                this.videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                video.srcObject = this.videoStream;
                video.play();
                this.scannerActive = true;
                this.scanning = true;

                video.onloadedmetadata = () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                };

                const self = this;
                function scan() {
                    if (!self.scannerActive) return;

                    const now = Date.now();
                    if (now - lastScannedTime > scanInterval && !self.processing && !self.showAlert) {
                        try {
                            ctx.drawImage(video, 0, 0);
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(imageData.data, canvas.width, canvas.height);

                            if (code && code.data) {
                                const qrToken = code.data.trim();
                                const staffId = self.extractStaffId(qrToken);
                                if (staffId && (staffId !== self.lastScannedId || now - self.lastScanTime > 5000)) {
                                    self.lastScannedId = staffId;
                                    self.lastScanTime = now;
                                    self.processScan(qrToken, staffId);
                                } else if (!staffId) {
                                    const shouldNotify = self.lastInvalidQr !== qrToken || (now - self.lastInvalidQrAt > 3000);
                                    if (shouldNotify) {
                                        self.lastInvalidQr = qrToken;
                                        self.lastInvalidQrAt = now;
                                        self.statusType = 'error';
                                        self.statusMessage = 'Unrecognized QR detected. Please scan a valid staff QR.';
                                    }
                                }
                            }
                            lastScannedTime = now;
                        } catch (e) {
                            console.warn('Scan frame error:', e);
                        }
                    }
                    requestAnimationFrame(scan);
                }

                scan();
            } catch (e) {
                console.error('Camera error:', e);
                this.error = 'Camera access denied. Please allow camera access.';
                this.scanning = false;
            }
        },

        stopScanner() {
            this.scannerActive = false;
            this.scanning = false;
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(track => track.stop());
                this.videoStream = null;
            }
        },

        extractStaffId(qrData) {
            const trimmed = qrData.trim();
            if (/^\d+$/.test(trimmed)) {
                return parseInt(trimmed, 10);
            }
            try {
                const parsed = JSON.parse(trimmed);
                if (parsed.staff_id) return parseInt(parsed.staff_id, 10);
                if (parsed.id) return parseInt(parsed.id, 10);
            } catch (e) {}
            const match = trimmed.match(/staff[_-]?id[=:]\s*(\d+)/i);
            if (match) return parseInt(match[1], 10);
            return null;
        },

        async processScan(qrToken, staffId = null) {
            this.processing = true;
            this.error = '';
            this.statusType = 'info';
            this.statusMessage = 'Processing scan...';

            try {
                const payload = {
                    qr_token: qrToken,
                };

                if (staffId) {
                    payload.staff_id = staffId;
                }

                if (this.selectedSite && this.selectedSite !== 'other') {
                    payload.site_id = parseInt(this.selectedSite, 10);
                } else if (this.isOtherSite && this.customSiteName) {
                    payload.custom_site_name = this.customSiteName;
                }

                const res = await axios.post('/api/v1/pwa-qr-attendance/scan', payload);

                if (res.data.success) {
                    const d = res.data.data;
                    const action = res.data.action;

                    if (action === 'check_in') {
                        this.statusType = 'success';
                        this.statusMessage = d.staff_name + ' checked in at ' + d.check_in_time + '.';
                        this.alertData = {
                            icon: '✅',
                            title: 'Checked In!',
                            staffName: d.staff_name,
                            timeInfo: 'Check-in time: <strong>' + d.check_in_time + '</strong>',
                            overtimeMinutes: 0,
                            btnClass: 'alert-btn-success',
                            btnText: 'OK, Continue Scanning',
                        };
                    } else {
                        this.statusType = 'success';
                        this.statusMessage = d.staff_name + ' checked out at ' + d.check_out_time + '.';
                        this.alertData = {
                            icon: '🏁',
                            title: 'Checked Out!',
                            staffName: d.staff_name,
                            timeInfo: 'In: <strong>' + d.check_in_time + '</strong> — Out: <strong>' + d.check_out_time + '</strong><br>Hours worked: <strong>' + d.hours_worked + '</strong><br>Overtime: <strong>' + (d.overtime_display || '0h') + '</strong>',
                            overtimeMinutes: d.overtime_minutes || 0,
                            btnClass: 'alert-btn-primary',
                            btnText: 'OK, Continue Scanning',
                        };
                    }
                    this.showAlert = true;
                } else {
                    this.error = res.data.message || 'Scan failed.';
                    this.statusType = 'error';
                    this.statusMessage = this.error;
                }
            } catch (e) {
                console.error('Scan error:', e);
                const msg = e.response?.data?.message || 'Failed to process scan.';
                this.error = msg;
                this.statusType = 'error';
                this.statusMessage = msg;
            } finally {
                this.processing = false;
            }
        },

        submitManualToken() {
            const token = this.manualQrToken.trim();
            if (!token) {
                return;
            }
            this.processScan(token);
            this.manualQrToken = '';
        },

        dismissAlert() {
            this.showAlert = false;
            this.alertData = {};
        },

        async loadTodayRecords() {
            this.loadingRecords = true;
            try {
                const res = await axios.get('/api/v1/pwa-qr-attendance/today');
                if (res.data.success) {
                    this.records = res.data.data.records;
                    this.summary = res.data.data.summary;
                }
            } catch (e) {
                console.error('Failed to load records:', e);
                this.error = 'Failed to load today\'s records.';
            } finally {
                this.loadingRecords = false;
            }
        },
    };
}
</script>
@endsection
