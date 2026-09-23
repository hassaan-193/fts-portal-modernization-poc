@extends('pwa.app')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .history-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: #f8f9fa;
    }

    .header {
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 4px 8px;
    }

    .header h1 {
        flex:  1;
        font-size: 16px;
        margin: 0;
        font-weight: 600;
    }

    .filter-section {
        background: white;
        padding: 12px 16px;
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        border-bottom: 1px solid #f0f0f0;
        overflow-x: auto;
    }

    .filter-btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 16px;
        background: white;
        font-size: 12px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
        color: #666;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        border-color: #FF6B5B;
    }

    .filter-btn:hover {
        border-color: #FF6B5B;
    }

    .date-range-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 12px;
        background: white;
    }

    .content-wrapper {
        flex: 1;
        overflow-y: auto;
        padding: 8px 16px;
    }

    .date-group {
        margin-bottom: 16px;
    }

    .date-header {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        padding: 12px 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .history-item {
        background: white;
        padding: 12px 14px;
        margin-bottom:  8px;
        border-radius: 8px;
        border-left: 4px solid #FF6B5B;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .history-item:active {
        transform: scale(0.98);
    }

    .history-content {
        flex: 1;
    }

    .history-time {
        font-size: 11px;
        color: #999;
        margin-bottom: 2px;
    }

    .history-labor {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .history-details {
        font-size: 11px;
        color: #999;
    }

    .history-status {
        font-size: 11px;
        padding: 6px 10px;
        border-radius:  6px;
        display: inline-block;
        font-weight: 600;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .status-pending {
        background: #FFF5F3;
        color: #FF6B5B;
    }

    .status-approved {
        background: #F0FFF4;
        color: #22863a;
    }

    .status-rejected {
        background: #FFF5F5;
        color: #cb2431;
    }

    .status-absent {
        background: #FFE8E8;
        color: #FF6B5B;
    }

    .status-present {
        background: #E8F5E9;
        color: #22863a;
    }

    .loading {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 13px;
    }

    .empty {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size:  13px;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color:   #c33;
        padding: 10px 14px;
        margin: 0 16px 8px 16px;
        border-radius:  6px;
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

    .stats-row {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        padding: 0 16px;
    }

    .stat-card {
        flex: 1;
        background: white;
        padding: 10px 12px;
        border-radius:  8px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .stat-number {
        font-size: 18px;
        font-weight:  700;
        color: #FF6B5B;
    }

    .stat-label {
        font-size: 10px;
        color: #999;
        margin-top: 2px;
    }

    .no-filter-match {
        text-align: center;
        padding: 40px 20px;
        color:  #999;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="history-container" x-data="historyApp()">
    
    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <h1>Attendance History</h1>
    </div>

    <template x-if="error">
        <div class="error-alert">
            <span x-text="error"></span>
            <button @click="error = ''" type="button">×</button>
        </div>
    </template>

    <!-- Stats Section -->
    <template x-if="!loading && history.length > 0">
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number" x-text="getStats().total"></div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" x-text="getStats().absent"></div>
                <div class="stat-label">Absent</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" x-text="getStats().present"></div>
                <div class="stat-label">Present</div>
            </div>
        </div>
    </template>

    <!-- Filter Section -->
    <div class="filter-section">
        <button class="filter-btn" : class="{ active: activeFilter === 'all' }" @click="setFilter('all')">
            All
        </button>
        <button class="filter-btn" : class="{ active: activeFilter === 'absent' }" @click="setFilter('absent')">
            Absent
        </button>
        <button class="filter-btn" :class="{ active:  activeFilter === 'present' }" @click="setFilter('present')">
            Present
        </button>
        <button class="filter-btn" :class="{ active: activeFilter === 'pending' }" @click="setFilter('pending')">
            Pending
        </button>
        <input type="date" class="date-range-input" x-model="filterDate" @change="filterByDate()">
    </div>

    <!-- History List -->
    <div class="content-wrapper">
        <template x-if="loading">
            <div class="loading">
                <p>Loading history...</p>
            </div>
        </template>

        <template x-if="! loading && filteredHistory.length > 0">
            <div>
                <template x-for="group in groupedHistory" :key="group.date">
                    <div class="date-group">
                        <div class="date-header" x-text="formatDateHeader(group.date)"></div>
                        <template x-for="record in group.records" :key="record.id">
                            <div class="history-item">
                                <div class="history-content">
                                    <div class="history-time" x-text="formatTime(record.created_at)"></div>
                                    <div class="history-labor" x-text="record.labor_name"></div>
                                    <div class="history-details">
                                        <span x-text="record.role || 'N/A'"></span>
                                        <span> · ID: #<span x-text="record.labor_id"></span></span>
                                    </div>
                                </div>
                                <span : class="'history-status status-' + (record.status || 'pending')" x-text="(record.status || 'pending').toUpperCase()"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!loading && history.length > 0 && filteredHistory.length === 0">
            <div class="no-filter-match">
                <p>No records match the selected filter</p>
            </div>
        </template>

        <template x-if="!loading && history.length === 0">
            <div class="empty">
                <p>No attendance records yet</p>
            </div>
        </template>
    </div>

</div>

<script>
function historyApp() {
    return {
        history: [],
        filteredHistory: [],
        groupedHistory: [],
        loading: true,
        error: '',
        token: localStorage.getItem('foreman_token'),
        activeFilter: 'all',
        filterDate: '',

        init() {
            if (! this.token) {
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.loadHistory();
        },

        async loadHistory() {
            try {
                console.log('Loading history...');
                const res = await axios.get('/api/v1/attendance/history');
                console.log('History response:', res.data);
                
                if (res.data.success) {
                    this.history = res.data.data || [];
                    this.filteredHistory = this.history.slice();
                    this.groupByDate();
                    console.log('Loaded ' + this.history.length + ' records');
                }
            } catch (err) {
                console.error('Error:', err);
                this.error = err.response?.data?.message || 'Failed to load history';
            } finally {
                this.loading = false;
            }
        },

        setFilter(filter) {
            this.activeFilter = filter;
            this.applyFilters();
        },

        filterByDate() {
            this.applyFilters();
        },

        applyFilters() {
            this.filteredHistory = this.history.filter(record => {
                // Status filter
                if (this.activeFilter !== 'all' && record.status !== this.activeFilter) {
                    return false;
                }

                // Date filter
                if (this.filterDate) {
                    const recordDate = new Date(record.attendance_date).toISOString().split('T')[0];
                    if (recordDate !== this.filterDate) {
                        return false;
                    }
                }

                return true;
            });

            this.groupByDate();
        },

        groupByDate() {
            const grouped = {};

            this.filteredHistory.forEach(record => {
                const date = record.attendance_date || record.created_at.split(' ')[0];
                if (! grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(record);
            });

            // Sort dates in descending order
            this.groupedHistory = Object.keys(grouped)
                .sort((a, b) => new Date(b) - new Date(a))
                .map(date => ({
                    date:  date,
                    records: grouped[date]
                }));
        },

        formatDateHeader(dateStr) {
            const date = new Date(dateStr);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            if (date.toDateString() === today.toDateString()) {
                return 'Today, ' + months[date.getMonth()] + ' ' + date.getDate();
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday, ' + months[date.getMonth()] + ' ' + date.getDate();
            } else {
                return days[date.getDay()] + ', ' + months[date.getMonth()] + ' ' + date.getDate();
            }
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            const hours = String(date.getHours()).padStart(2, '0');
            const mins = String(date.getMinutes()).padStart(2, '0');
            return hours + ':' + mins;
        },

        getStats() {
            return {
                total:  this.filteredHistory.length,
                absent: this.filteredHistory.filter(r => r.status === 'absent').length,
                present: this.filteredHistory.filter(r => r.status === 'present').length
            };
        }
    };
}
</script>
@endsection