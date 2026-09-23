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
<style>

    .labor-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 16px 100px;
    }

    .labor-card {
        position: relative;
        display: flex;
        align-items: center;
        background: white;
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .labor-card.selected {
        border-color: #FF6B5B;
        background: #FFF5F3;
    }

    .labor-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }

    .labor-info {
        flex: 1;
        min-width: 0;
        padding-left: 10px;
    }

    .labor-name {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }

    .labor-role {
        font-size: 11px;
        color: #999;
        margin: 2px 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .overtime-input {
        width: 72px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 4px 6px;
        text-align: center;
        font-size: 14px;
        margin-left: 10px;
    }

    #checkbox {
        width: 32px;
        height: 32px;
        border: 2px solid #ddd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #ddd;
        font-size: 18px;
        font-weight: bold;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    #checkbox.checked {
        background: #FF6B5B;
        border-color: #FF6B5B;
        color: white;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        color: #FF6B5B;
        margin-bottom: 8px;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: #FF6B5B;
        box-shadow: 0 0 0 2px rgba(255, 107, 91, 0.15);
    }
</style>
@endsection

@section('content')
<div class="attendance-container" x-data="attendanceApp()">
    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <div class="header-content">
            <h1>Present Labor</h1>
            <p>Mark Present Attendance with Overtime</p>
        </div>
        <button @click="if(confirm('Logout?')) { localStorage.removeItem('foreman_token'); window.location.href='/pwa/login'; }" style="color: white; font-size: 20px;">➜</button>
    </div>

    <!-- Date Card -->
    <div class="attendance-section">
        <div class="attendance-card">
            <div class="icon">📅</div>
            <div class="date-content">
                <h3 x-text="yesterdayLabel"></h3>
                <p>Overtime Date (Yesterday)</p>
            </div>
        </div>
    </div>

    <!-- Site Selection -->
    <div class="form-group" style="margin: 12px 16px;">
        <label for="site">Select Site</label>
        <select id="site" x-model="selectedSite" @change="isOtherSite = (selectedSite == 'other'); if (!isOtherSite) customSiteName='';">
            <option value="" disabled selected>Select a site</option>
            <template x-for="site in sites" :key="site.id">
                <option :value="site.id" x-text="site.site_name"></option>
            </template>
            <option value="other">Other</option>
        </select>
    </div>

    <!-- Custom Site Input -->
    <template x-if="isOtherSite">
        <div class="form-group" style="margin: 12px 16px;">
            <label for="customSite">Custom Site Name</label>
            <input type="text" id="customSite" x-model="customSiteName" placeholder="Enter custom site name">
        </div>
    </template>

    <!-- Search Input -->
    <div class="search-section">
        <input type="text" class="search-input" placeholder="Search Labor" x-model="search" @keyup="filterLabors()">
    </div>

    <!-- Error message -->
    <template x-if="error">
        <div class="error-alert">
            <span x-text="error"></span>
            <button @click="error = ''" type="button">×</button>
        </div>
    </template>

    <!-- Labor List with Checkbox, Overtime, Present Selection -->
    <template x-if="!loading">
        <div class="labor-list">
<template x-for="labor in filteredLabors" :key="labor.id">
    <div :class="'labor-card' + (isSelected(labor.id) ? ' selected' : '')" @click="toggleLabor(labor.id)">
        <div class="labor-avatar" :style="'background: ' + getColor(labor.id)">
            <span x-text="getInitials(labor.name)"></span>
        </div>
        <div class="labor-info">
            <div class="labor-name" x-text="labor.name"></div>
            <div class="labor-role">ID: <span x-text="labor.id"></span></div>
        </div>
        
        <!-- Overtime Input -->
        <input class="overtime-input"
               type="number"
               x-model="labor.overtime"
               placeholder="OT Hours"
               :disabled="!isSelected(labor.id)"
               @click.stop><!-- Prevent click event from propagating -->
               
        <!-- Checkbox -->
        <div :class="'checkbox' + (isSelected(labor.id) ? ' checked' : '')" id="checkbox">
            <span x-show="isSelected(labor.id)">✓</span>
        </div>
    </div>
</template>

        </div>
    </template>
    <div class="submit-section">
        <button class="btn-submit" @click="submitAttendance()" :disabled="selectedLabors().length === 0 || submitting">
            <span x-show="!submitting">
                ✓ Mark Present
                <span class="badge" x-text="selectedLabors().length"></span>
            </span>
            <span x-show="submitting">Submitting...</span>
        </button>
        <p class="submit-hint" x-text="selectedLabors().length + ' out of ' + labors.length + ' marked present'"></p>
    </div>
</div>

<script>
function attendanceApp() {
    return {
        labors: [],
        filteredLabors: [],
        sites: [],
        selectedIds: [],
        search: '',
        loading: true,
        error: '',
        submitting: false,
        todayLabel: '',
        yesterdayLabel: '',
        selectedSite: '',
        customSiteName: '',
        isOtherSite: false,
        token: localStorage.getItem('foreman_token'),

        init() {
            if (!this.token) { window.location.href = '/pwa/login'; return; }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            const today = new Date();
            this.todayLabel = today.toDateString();
            
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            this.yesterdayLabel = yesterday.toDateString();
            
            this.loadDefaultState();
        },

        async loadDefaultState() {
            try {
                this.loading = true;
                const { data } = await axios.get('/api/v1/present-attendance/today');
                this.labors = data.data.labors.map(labor => ({ ...labor, overtime: '' }));
                this.filteredLabors = this.labors.slice();
                this.sites = data.data.sites;
            } catch (e) {
                this.error = 'Failed to load labors.';
            } finally {
                this.loading = false;
            }
        },

        filterLabors() {
            const q = this.search.toLowerCase();
            this.filteredLabors = this.labors.filter(labor =>
                labor.name.toLowerCase().includes(q) ||
                String(labor.id).includes(q)
            );
        },

        toggleLabor(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx > -1) this.selectedIds.splice(idx, 1);
            else this.selectedIds.push(id);
        },

        isSelected(id) {
            return this.selectedIds.indexOf(id) >= 0;
        },

        selectedLabors() {
            return this.labors.filter(labor => this.isSelected(labor.id));
        },

        getInitials(name) {
            return name.split(' ').map(n => n.charAt(0).toUpperCase()).join('');
        },

        getColor(id) {
            const colors = ['#00f2fe', '#43e97b', '#4facfe', '#fa709a', '#764ba2'];
            return colors[id % colors.length];
        },

        async submitAttendance() {
            if (!this.selectedSite && !this.customSiteName) {
                return alert('Please select a valid site or enter a custom site name.');
            }

            // Calculate yesterday's date for overtime submission (local date, not UTC)
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayString = [
                yesterday.getFullYear(),
                String(yesterday.getMonth() + 1).padStart(2, '0'),
                String(yesterday.getDate()).padStart(2, '0'),
            ].join('-');

            // Fix for site_id and custom_site_name
            const payload = {
                attendance_date: yesterdayString,
                site_id: this.selectedSite !== 'other' ? this.selectedSite : null, // Send null if "other" is selected
                custom_site_name: this.selectedSite === 'other' ? this.customSiteName : null, // Only send custom site name when "other" is selected
                labors: this.selectedLabors().map(labor => ({
                    labor_id: labor.id,
                    overtime_hours: labor.overtime || 0,
                })),
            };

            // Send the payload
            try {
                await axios.post('/api/v1/present-attendance/submit', payload);
                alert('Attendance submitted successfully!');
                window.location.href = '/pwa/confirmation';
            } catch (error) {
                console.error('Submission Failed:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    alert('Failed to submit attendance: ' + error.response.data.message);
                } else {
                    alert('Failed to submit attendance. Please check your input and try again.');
                }
            }
        },
    };
}
</script>
@endsection