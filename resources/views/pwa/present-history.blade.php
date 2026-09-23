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
        flex: 1;
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
        margin-bottom: 8px;
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
        border-radius: 6px;
        display: inline-block;
        font-weight: 600;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .status-present {
        background: #FFF5F3;
        color: #FF6B5B;
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
        font-size: 13px;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color: #c33;
        padding: 10px 14px;
        margin: 0 16px 8px 16px;
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
</style>
@endsection

@section('content')
<div class="history-container" x-data="historyApp()">

    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <h1>Submitted Presents</h1>
    </div>

    <template x-if="error">
        <div class="error-alert">
            <span x-text="error"></span>
            <button @click="error = ''" type="button">×</button>
        </div>
    </template>

    <!-- Filter Section -->
    <div class="filter-section">
        <input type="date" class="date-range-input" x-model="filterStartDate" placeholder="Start Date">
        <input type="date" class="date-range-input" x-model="filterEndDate" placeholder="End Date">
        <button class="filter-btn" @click="applyDateFilter()">Apply</button>
    </div>

    <!-- History List -->
    <div class="content-wrapper">
        <template x-if="loading">
            <div class="loading">
                <p>Loading records...</p>
            </div>
        </template>

        <template x-if="!loading && filteredHistory.length > 0">
            <div>
                <template x-for="record in filteredHistory" :key="record.id">
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-time" x-text="record.date"></div>
                            <div class="history-labor" x-text="record.labor_name"></div>
                            <div class="history-details">
                                Overtime: <span x-text="record.overtime_hours"></span> hrs,
                                Site: <span x-text="record.site_name"></span>
                            </div>
                        </div>
                        <span class="history-status status-present">PRESENT</span>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!loading && filteredHistory.length === 0">
            <div class="empty">
                <p>No records found for the selected date range.</p>
            </div>
        </template>
    </div>
</div>

<script>
function historyApp() {
    return {
        history: [],
        filteredHistory: [],
        loading: true,
        error: '',
        filterStartDate: '',
        filterEndDate: '',
        token: localStorage.getItem('foreman_token'),

        init() {
            if (!this.token) {
                window.location.href = '/pwa/login';
                return;
            }

            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.loadHistory();
        },

        async loadHistory() {
            try {
                const response = await axios.get('/api/v1/present-attendance/history');
                if (response.data.success) {
                    this.history = response.data.data || [];
                    this.filteredHistory = this.history;
                } else {
                    this.error = response.data.message || 'Failed to load records.';
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'An error occurred while fetching data.';
            } finally {
                this.loading = false;
            }
        },

        applyDateFilter() {
            const startDate = new Date(this.filterStartDate);
            const endDate = new Date(this.filterEndDate);

            this.filteredHistory = this.history.filter(record => {
                const recordDate = new Date(record.date);
                return recordDate >= startDate && recordDate <= endDate;
            });
        }
    };
}
</script>
@endsection