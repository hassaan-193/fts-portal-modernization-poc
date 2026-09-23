@extends('pwa.app')

@section('styles')
<style>
    .sites-container {
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

    .header h1 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        flex: 1;
        text-align: center;
    }

    .header button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 4px 8px;
    }

    .content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .search-section {
        padding: 12px 16px;
        flex-shrink: 0;
        background: white;
        border-bottom: 1px solid #f0f0f0;
    }

    .search-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        font-size: 13px;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: #FF6B5B;
        box-shadow: 0 0 0 2px rgba(255, 107, 91, 0.1);
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color: #c33;
        padding: 10px 14px;
        margin: 8px 16px 0 16px;
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

    .success-alert {
        background: #EFE;
        border-left: 4px solid #43a047;
        color: #2a6e2a;
        padding: 10px 14px;
        margin: 8px 16px 0 16px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    .success-alert button {
        background: none;
        border: none;
        color: #2a6e2a;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
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

    .sites-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 16px;
        padding-bottom: 80px;
    }

    .sites-list::-webkit-scrollbar {
        width: 4px;
    }

    .sites-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .sites-list::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 2px;
    }

    .site-card {
        display: flex;
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        padding: 14px 12px;
        margin-bottom: 10px;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .site-card:active {
        transform: scale(0.98);
    }

    .site-info {
        flex: 1;
        min-width: 0;
    }

    .site-name {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }

    .site-meta {
        font-size: 11px;
        color: #999;
        margin: 4px 0 0 0;
    }

    .site-actions {
        display: flex;
        gap: 8px;
        margin-left: 12px;
    }

    .btn-edit, .btn-delete {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        padding: 6px 8px;
        transition: all 0.2s ease;
    }

    .btn-edit {
        color: #FF6B5B;
    }

    .btn-edit:active {
        transform: scale(1.1);
    }

    .btn-delete {
        color: #FF6B5B;
    }

    .btn-delete:active {
        transform: scale(1.1);
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
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

    .btn-add {
        width: 100%;
        padding: 12px 16px;
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        border: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(255, 107, 91, 0.25);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-add:active {
        transform: scale(0.98);
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        align-items: flex-end;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .delete-modal {
        background: white;
        width: 100%;
        padding: 20px 16px;
        border-radius: 16px 16px 0 0;
        box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.15);
    }

    .delete-modal h3 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 8px 0;
    }

    .delete-modal p {
        font-size: 13px;
        color: #666;
        margin: 0 0 16px 0;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
    }

    .btn-cancel, .btn-confirm-delete {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cancel {
        background: #f0f0f0;
        color: #666;
    }

    .btn-cancel:active {
        transform: scale(0.98);
    }

    .btn-confirm-delete {
        background: #FF6B5B;
        color: white;
    }

    .btn-confirm-delete:active {
        transform: scale(0.98);
    }

    .btn-confirm-delete:disabled {
        background: #ddd;
        color: #999;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="sites-container" x-data="sitesApp()">
    
    <!-- HEADER -->
    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <h1>Sites</h1>
        <button @click="logout()" style="color: white; font-size: 20px;">Logout</button>
    </div>

    <!-- CONTENT WRAPPER -->
    <div class="content-wrapper">
        
        <!-- SEARCH SECTION -->
        <div class="search-section">
            <input type="text" class="search-input" placeholder="Search sites..." x-model="search" @keyup="filterSites()">
        </div>

        <!-- SUCCESS ALERT -->
        <template x-if="successMessage">
            <div class="success-alert">
                <span x-text="successMessage"></span>
                <button @click="successMessage = ''" type="button">×</button>
            </div>
        </template>

        <!-- ERROR ALERT -->
        <template x-if="error">
            <div class="error-alert">
                <span x-text="error"></span>
                <button @click="error = ''" type="button">×</button>
            </div>
        </template>

        <!-- LOADING -->
        <template x-if="loading">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p style="font-size: 12px; color: #999;">Loading sites...</p>
            </div>
        </template>

        <!-- SITES LIST -->
        <template x-if="!loading">
            <div class="sites-list">
                <template x-for="site in filteredSites" :key="site.id">
                    <div class="site-card">
                        <div class="site-info">
                            <p class="site-name" x-text="site.site_name"></p>
                            <p class="site-meta" x-text="'Created: ' + site.created_at"></p>
                        </div>
                        <div class="site-actions">
                            <button class="btn-edit" @click="editSite(site.id)" title="Edit">✎</button>
                            <button class="btn-delete" @click="openDeleteModal(site.id, site.site_name)" title="Delete">🗑</button>
                        </div>
                    </div>
                </template>
                
                <template x-if="filteredSites.length === 0">
                    <div class="no-results">No sites found</div>
                </template>
            </div>
        </template>
    </div>

    <!-- ADD BUTTON -->
    <div class="submit-section">
        <button class="btn-add" @click="goToCreate()">
            ✓ Add New Site
        </button>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div :class="'modal-backdrop' + (showDeleteModal ? ' show' : '')" @click.self="showDeleteModal = false">
        <div class="delete-modal" @click.stop>
            <h3>Delete Site?</h3>
            <p x-text="'Are you sure you want to delete: ' + (siteToDelete.name || 'this site') + '?'"></p>
            <div class="modal-actions">
                <button class="btn-cancel" @click="showDeleteModal = false">Cancel</button>
                <button class="btn-confirm-delete" @click="confirmDelete()" :disabled="deleting">
                    <span x-show="!deleting">Delete</span>
                    <span x-show="deleting">Deleting...</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function sitesApp() {
    return {
        sites: [],
        filteredSites: [],
        search: '',
        loading: true,
        error: '',
        successMessage: '',
        token: localStorage.getItem('foreman_token'),
        showDeleteModal: false,
        deleting: false,
        siteToDelete: { id: null, name: '' },

        init() {
            console.log('Sites app initialized');
            if (!this.token) {
                console.log('No token found, redirecting to login');
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.loadSites();
        },

        async loadSites() {
            try {
                console.log('Loading sites...');
                const res = await axios.get('/api/v1/sites');
                console.log('Response:', res.data);
                if (res.data.success) {
                    this.sites = res.data.data;
                    this.filteredSites = this.sites.slice();
                    console.log('Loaded ' + this.sites.length + ' sites');
                }
            } catch (err) {
                console.error('Error loading sites:', err);
                this.error = err.response?.data?.message || 'Failed to load sites';
            } finally {
                this.loading = false;
            }
        },

        filterSites() {
            const q = this.search.toLowerCase();
            this.filteredSites = this.sites.filter(function(site) {
                return site.site_name.toLowerCase().indexOf(q) >= 0;
            });
        },

        goToCreate() {
            window.location.href = '/pwa/sites/create';
        },

        editSite(id) {
            window.location.href = '/pwa/sites/' + id + '/edit';
        },

        openDeleteModal(id, name) {
            console.log('Opening delete modal - ID:', id, 'Name:', name);
            this.siteToDelete = { 
                id: id, 
                name: name 
            };
            this.showDeleteModal = true;
            console.log('siteToDelete set to:', this.siteToDelete);
        },

        async confirmDelete() {
            if (!this.siteToDelete.id) {
                console.log('No site ID to delete');
                return;
            }
            
            this.deleting = true;
            try {
                console.log('Deleting site ID:', this.siteToDelete.id);
                const res = await axios.delete('/api/v1/sites/' + this.siteToDelete.id);
                console.log('Delete response:', res.data);
                
                if (res.data.success) {
                    this.successMessage = 'Site deleted successfully';
                    this.showDeleteModal = false;
                    this.siteToDelete = { id: null, name: '' };
                    // Reload sites
                    setTimeout(() => this.loadSites(), 1000);
                }
            } catch (err) {
                console.error('Delete error:', err);
                this.error = err.response?.data?.message || 'Failed to delete site';
                this.showDeleteModal = false;
            } finally {
                this.deleting = false;
            }
        },

        logout() {
            if (confirm('Logout?')) {
                localStorage.removeItem('foreman_token');
                window.location.href = '/pwa/login';
            }
        }
    };
}
</script>
@endsection