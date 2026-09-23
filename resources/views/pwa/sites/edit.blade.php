@extends('pwa.app')

@section('styles')
<style>
    .edit-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background:  #f8f9fa;
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding:  12px 16px;
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
        flex:  1;
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
        overflow-y: auto;
        padding: 20px 16px;
        padding-bottom: 100px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius:  8px;
        font-size:  16px;
        transition:  border-color 0.2s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input: disabled {
        background: #f5f5f5;
        color: #999;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color: #c33;
        padding: 12px;
        border-radius: 6px;
        margin-bottom:  20px;
        font-size: 13px;
    }

    .error-item {
        margin:  4px 0;
    }

    .success-alert {
        background: #EFE;
        border-left: 4px solid #43a047;
        color: #2a6e2a;
        padding: 12px;
        border-radius:  6px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex:  1;
        gap: 12px;
    }

    .spinner {
        width: 36px;
        height: 36px;
        border: 3px solid #f0f0f0;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
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
        padding:  12px 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 20px;
        font-size:  14px;
        font-weight:  600;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(102, 126, 234, 0.25);
        transition: all 0.2s ease;
    }

    .btn-submit:active: not(:disabled) {
        transform:  scale(0.98);
    }

    .btn-submit:disabled {
        background: #e8e8e8;
        color: #bbb;
        cursor: not-allowed;
        box-shadow: none;
    }
</style>
@endsection

@section('content')
<div class="edit-container" x-data="editSiteApp({{ $siteId }})">
    
    <!-- HEADER -->
    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <h1>Edit Site</h1>
        <div></div>
    </div>

    <!-- CONTENT -->
    <div class="content-wrapper">
        
        <!-- LOADING -->
        <template x-if="loading">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p style="font-size: 12px; color: #999;">Loading site...</p>
            </div>
        </template>

        <!-- FORM -->
        <template x-if="!loading">
            <div>
                <!-- ERROR ALERT -->
                <template x-if="error">
                    <div class="error-alert">
                        <template x-if="typeof errors === 'object' && Object.keys(errors).length > 0">
                            <div>
                                <template x-for="field in Object.keys(errors)">
                                    <div class="error-item" x-text="errors[field][0]"></div>
                                </template>
                            </div>
                        </template>
                        <template x-if="typeof errors !== 'object' || Object.keys(errors).length === 0">
                            <span x-text="error"></span>
                        </template>
                    </div>
                </template>

                <!-- SUCCESS ALERT -->
                <template x-if="successMessage">
                    <div class="success-alert" x-text="successMessage"></div>
                </template>

                <form @submit.prevent="submit()">
                    <div class="form-group">
                        <label for="site_name">Site Name *</label>
                        <input
                            id="site_name"
                            type="text"
                            x-model="site_name"
                            placeholder="Enter site name"
                            required
                            maxlength="255"
                            @keydown="error = ''"
                        >
                    </div>
                </form>
            </div>
        </template>
    </div>

    <!-- SUBMIT BUTTON -->
    <template x-if="!loading">
        <div class="submit-section">
            <button class="btn-submit" @click="submit()" :disabled="!site_name || submitting">
                <span x-show="!submitting">✓ Update Site</span>
                <span x-show="submitting">Updating...</span>
            </button>
        </div>
    </template>

</div>

<script>
function editSiteApp(siteId) {
    return {
        siteId: siteId,
        site_name: '',
        loading:  true,
        submitting: false,
        error: '',
        errors: {},
        successMessage:  '',
        token: localStorage.getItem('foreman_token'),

        init() {
            if (! this.token) {
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            this.loadSite();
        },

        async loadSite() {
            try {
                console.log('Loading site...');
                const res = await axios.get('/api/v1/sites/' + this.siteId);
                console.log('Response:', res.data);
                if (res.data.success) {
                    this.site_name = res.data.data.site_name;
                    this.loading = false;
                }
            } catch (err) {
                console.error('Error:', err);
                this.error = 'Failed to load site';
                this.loading = false;
            }
        },

        async submit() {
            if (!this.site_name.trim()) {
                this.error = 'Please enter a site name';
                return;
            }

            this.submitting = true;
            this.error = '';
            this.errors = {};
            this.successMessage = '';

            try {
                const res = await axios.put('/api/v1/sites/' + this.siteId, {
                    site_name: this.site_name
                });

                console.log('Update response:', res.data);

                if (res.data.success) {
                    this.successMessage = 'Site updated successfully';
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = '/pwa/sites';
                    }, 1500);
                } else {
                    this.error = res.data.message || 'Failed to update site';
                    this.submitting = false;
                }
            } catch (err) {
                console.error('Update error:', err);
                
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors || {};
                    this.error = 'Validation failed';
                } else {
                    this.error = err.response?.data?.message || 'Failed to update site.Please try again.';
                }
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection