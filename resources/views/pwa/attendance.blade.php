@extends('pwa.app')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .\(isSelected\(labor\.id\).selected\' {
        display:flex;
        padding:1%;
        align-items:center;
    }

    .attendance-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background:   #f8f9fa;
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

    .content-wrapper {
        flex: 1;
        display:  flex;
        flex-direction:  column;
        overflow: hidden;
    }

    .attendance-section {
        padding: 12px 16px;
        flex-shrink: 0;
    }

    .attendance-card {
        background: white;
        border:   2px solid #FF6B5B;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .attendance-card .icon {
        font-size: 24px;
    }

    .date-content h3 {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }

    .date-content p {
        font-size: 11px;
        color: #FF6B5B;
        margin: 2px 0 0 0;
    }

    .search-section {
        padding: 12px 16px;
        flex-shrink: 0;
    }

    .search-input {
        width:   100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        font-size:   13px;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: #FF6B5B;
        box-shadow: 0 0 0 2px rgba(255, 107, 91, 0.1);
    }

    .search-input::placeholder {
        color: #999;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color: #c33;
        padding: 10px 14px;
        margin: 8px 16px 0 16px;
        border-radius:   6px;
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

    .install-banner {
        background: rgba(0, 0, 0, 0.2);
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }

    .install-banner-text {
        flex: 1;
        font-size: 12px;
        color: white;
    }

    .install-banner-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-install-banner {
        background: white;
        color: #FF6B5B;
        border:  none;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 11px;
        cursor: pointer;
    }

    .btn-dismiss-banner {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 11px;
        cursor: pointer;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex:   1;
        gap: 12px;
    }

    .spinner {
        width: 36px;
        height: 36px;
        border: 3px solid #f0f0f0;
        border-top-color: #FF6B5B;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .labor-list {
        flex:  1;
        overflow-y: auto;
        padding:   8px 16px;
        padding-bottom: 100px;
    }

    .labor-list:  :-webkit-scrollbar {
        width: 4px;
    }

    .labor-list: :-webkit-scrollbar-track {
        background:   transparent;
    }

    .labor-list::-webkit-scrollbar-thumb {
        background:  #ddd;
        border-radius:   2px;
    }

    .labor-card {
        display: flex;
        background: white;
        border:  2px solid transparent;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom:  8px;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .labor-card:active {
        transform: scale(0.98);
    }

    .labor-card.selected {
        border-color:   #FF6B5B;
        background-color:  #FFF9F7;
        box-shadow: 0 2px 6px rgba(255, 107, 91, 0.15);
    }
    .\(isSelected\(labor\.id\) .\(isSelected\(labor\.id\) span {
        color:   #FF6B5B;
    }

    .labor-avatar {
        width: 44px;
        height: 44px;
        border-radius:   50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .labor-info {
        flex: 1;
        min-width: 0;
        padding-left: 1%;
    }

    .labor-name {
        font-size: 14px;
        font-weight:   600;
        margin: 0;
        color: #2c3e50;
    }

    .labor-role {
        font-size: 11px;
        color: #999;
        margin: 2px 0 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #checkbox {
        width: 32px;
        height: 32px;
        border:   2px solid #ddd;
        border-radius:   50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #ddd;
        font-size: 18px;
        font-weight: bold;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    #checkbox.checked {
        background: #FF6B5B;
        border-color: #FF6B5B;
        color: white;
    }

    .no-results {
        text-align: center;
        padding: 30px 20px;
        color: #999;
        font-size: 13px;
    }

    .submit-section {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #f0f0f0;
        padding: 12px 16px 16px;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.08);
    }

    .btn-submit {
        width: 100%;
        padding:   12px 16px;
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        border: none;
        border-radius: 20px;
        font-size:   14px;
        font-weight:  600;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(255, 107, 91, 0.25);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-submit:active: not(:disabled) {
        transform: scale(0.98);
    }

    .btn-submit:disabled {
        background: #e8e8e8;
        color: #bbb;
        cursor: not-allowed;
        box-shadow: none;
    }

    .submit-hint {
        margin:   8px 0 0 0;
        text-align: center;
        font-size: 11px;
        color: #bbb;
    }

    .badge {
        background: rgba(255, 255, 255, 0.3);
        border-radius:  10px;
        padding: 2px 7px;
        font-size:   11px;
        font-weight:  600;
        min-width: 20px;
        text-align:  center;
    }
</style>
@endsection

@section('content')
<div class="attendance-container" x-data="attendanceApp()">
    
    <!-- INSTALL BANNER -->
    <template x-if="showInstallBanner">
        <div class="install-banner">
            <span class="install-banner-text">📲 Add app to home screen</span>
            <div class="install-banner-buttons">
                <button class="btn-install-banner" @click="installApp()">Install</button>
                <button class="btn-dismiss-banner" @click="dismissInstallBanner()">Dismiss</button>
            </div>
        </div>
    </template>

    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <div class="header-content">
            <h1>Absent Labor</h1>
            <p>Mark Absent For Today</p>
        </div>
        <button @click="installApp()" x-show="showInstallButton" style="color: white; font-size: 20px; display: flex;">⬇️</button>
        <button @click="if(confirm('Logout?')) { localStorage.removeItem('foreman_token'); window.location.href='/pwa/login'; }"  style="color: white; font-size: 20px;">➜]</button>    </div>

    <div class="content-wrapper">
        <div class="attendance-section">
            <div class="attendance-card">
                <div class="icon">📅</div>
                <div class="date-content">
                    <h3 x-text="todayLabel"></h3>
                    <p>Current Date</p>
                </div>
            </div>
        </div>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Search Labor" x-model="search" @keyup="filterLabors()">
        </div>

        <template x-if="error">
            <div class="error-alert">
                <span x-text="error"></span>
                <button @click="error = ''" type="button">×</button>
            </div>
        </template>

        <template x-if="loading">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p style="font-size: 12px; color: #999;">Loading labors...</p>
            </div>
        </template>

        <template x-if="!loading">
            <div class="labor-list">
                <template x-for="labor in filteredLabors" :key="labor.id">
                    <div :  class="'labor-card' + (isSelected(labor.id) ? ' selected' : '')" @click="toggleLabor(labor.id)">
                        <div class="labor-avatar" :  style="'background:   ' + getColor(labor.id)">
                            <span x-text="getInitials(labor.name)"></span>
                        </div>
                        <div class="labor-info">
                            <div class="labor-name" x-text="labor.full_name"></div>
                            <div class="labor-role" x-text="labor.role + ' · ID: #' + labor.id"></div>
                        </div>
                        <div :  class="'checkbox' + (isSelected(labor.id) ? ' checked' : '')" id="checkbox">
                            <span x-show="isSelected(labor.id)">✓</span>
                        </div>
                    </div>
                </template>
                <template x-if="filteredLabors.length === 0">
                    <div class="no-results">No labors found</div>
                </template>
            </div>
        </template>
    </div>

    <div class="submit-section">
        <button class="btn-submit" @click="submitAttendance()" :disabled="selected.length === 0 || submitting">
            <span x-show="!  submitting">
                ✓ Mark Absent
                <span class="badge" x-text="selected.length"></span>
            </span>
            <span x-show="submitting">Submitting...</span>
        </button>
        <p class="submit-hint" x-text="selected.length + ' out of ' + labors.length + ' marked absent'"></p>
    </div>
    
</div>

<script>
function attendanceApp() {
    return {
        labors: [],
        filteredLabors: [],
        selected: [],
        search: '',
        loading: true,
        error: '',
        submitting: false,
        todayLabel: '',
        token: localStorage.getItem('foreman_token'),
        showInstallBanner:  true,
        showInstallButton: true,
        deferredPrompt: null,

        init() {
            if (!   this.token) {
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.updateTodayLabel();
            this.setupInstallPrompt();
            this.loadLabors();
        },

        setupInstallPrompt() {
            const self = this;
            
            // Listen for beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                console.log('beforeinstallprompt fired');
                e.preventDefault();
                self. deferredPrompt = e;
                self.showInstallBanner = true;
                self.showInstallButton = true;
            });

            // Listen for app installed
            window.addEventListener('appinstalled', () => {
                console.log('App installed');
                self.showInstallBanner = false;
                self.showInstallButton = false;
            });

            // For testing - show banner anyway
            console.log('Setup complete');
        },

        installApp() {
            console.log('Install clicked');
            console.log('deferredPrompt:', this. deferredPrompt);
            
            if (this.deferredPrompt) {
                console.log('Showing prompt');
                this.deferredPrompt.prompt();
                this.deferredPrompt. userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted');
                    } else {
                        console. log('User dismissed');
                    }
                    this.deferredPrompt = null;
                    this.showInstallBanner = false;
                    this.showInstallButton = false;
                });
            } else {
                // Fallback for iOS
                alert('To install this app:\n\n1.  Tap Share button\n2. Select "Add to Home Screen"\n3. Tap "Add"');
            }
        },

        dismissInstallBanner() {
            this.showInstallBanner = false;
        },

        updateTodayLabel() {
            const d = new Date();
            const m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            this.todayLabel = 'Today ' + m[d.getMonth()] + ' ' + d.getDate();
        },

        getInitials(name) {
            return name.charAt(0).toUpperCase();
        },

        async loadLabors() {
            try {
                console.log('Loading labors.. .');
                const res = await axios.get('/api/v1/attendance/today');
                console.log('Response:', res.data);
                if (res.data.success) {
                    this.labors = res.data.data.labors;
                    this.filteredLabors = this.labors. slice();
                    console.log('Loaded ' + this.labors.length + ' labors');
                }
            } catch (err) {
                console.error('Error:', err);
                this.error = 'Failed to load labors';
            } finally {
                this.loading = false;
            }
        },

        filterLabors() {
            const q = this.search.toLowerCase();
            this.filteredLabors = this.labors.filter(function(labor) {
                return labor.name.toLowerCase().indexOf(q) >= 0 || 
                       labor.role.toLowerCase().indexOf(q) >= 0 || 
                       String(labor.id).indexOf(q) >= 0;
            });
        },

        toggleLabor(id) {
            console.log('Toggle labor:', id);
            const idx = this.selected.indexOf(id);
            if (idx > -1) {
                this.selected.splice(idx, 1);
            } else {
                this.selected.push(id);
            }
            console.log('Selected now:', this.selected);
        },

        isSelected(id) {
            return this.selected.indexOf(id) >= 0;
        },

        getColor(id) {
            const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe', '#43e97b', '#fa709a', '#FF6B5B'];
            return colors[id % colors.length];
        },

        async submitAttendance() {
            console.log('Submit clicked, selected:', this.selected. length);
            if (this.selected.length === 0) {
                this.error = 'Please select at least one labor';
                return;
            }
            this.submitting = true;
            try {
                const today = new Date();
                const dateStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today. getDate()).padStart(2, '0');
                
                const res = await axios.post('/api/v1/attendance/submit', {
                    absent_labor_ids: this.selected,
                    date: dateStr
                });
                
                if (res.data.success) {
                    const data = res.data.data;
                    window.location.href = '/pwa/confirmation? date=' + data.date + '&count=' + data.count;
                }
            } catch (err) {
                console.error('Submit error:', err);
                this. error = err.response?.data?.message || 'Failed to submit';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection