@extends('pwa.app')

@section('styles')
<style>
    .create-container {
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
        display:  flex;
        flex-direction:  column;
        overflow-y: auto;
        padding:  20px 16px;
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
        font-size:  16px ! important;
        transition: border-color 0.2s ease;
        background: white;
        color: #333;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input: disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
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

    .error-alert: first-child {
        margin-top: 0;
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
        font-size: 16px ! important;
    }

    .btn-submit:active: not(:disabled) {
        transform: scale(0.98);
    }

    .btn-submit:disabled {
        background: #e8e8e8;
        color: #bbb;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.6;
    }

    .form-hint {
        font-size: 11px;
        color: #999;
        margin-top: 4px;
    }
</style>
@endsection

@section('content')
<div class="create-container" x-data="createSiteApp()">
    
    <!-- HEADER -->
    <div class="header">
        <button onclick="window.history.back()">‹</button>
        <h1>Add New Site</h1>
        <div></div>
    </div>

    <!-- CONTENT -->
    <div class="content-wrapper">
        
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

        <!-- FORM -->
        <div class="form-group">
            <label for="site_name">Site Name <span style="color: #FF6B5B;">*</span></label>
            <input
                id="site_name"
                type="text"
                x-model="site_name"
                placeholder="Enter site name (e.g., Site A)"
                required
                maxlength="255"
                autocomplete="off"
                @input="error = ''"
            >
            <p class="form-hint" x-text="site_name.length + ' / 255 characters'"></p>
        </div>

    </div>

    <!-- SUBMIT BUTTON -->
    <div class="submit-section">
        <button class="btn-submit" @click="submit()" :disabled="!site_name.trim() || submitting">
            <span x-show="! submitting">✓ Create Site</span>
            <span x-show="submitting">Creating...</span>
        </button>
    </div>

</div>

<script>
function createSiteApp() {
    return {
        site_name: '',
        submitting: false,
        error: '',
        errors: {},
        token: localStorage.getItem('foreman_token'),

        init() {
            console.log('Create site app initialized');
            if (!this.token) {
                console.log('No token found, redirecting to login');
                window.location.href = '/pwa/login';
                return;
            }
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
            console.log('Token set in axios headers');
        },

        async submit() {
            console.log('Submit clicked, site_name:', this.site_name);

            if (!this.site_name.trim()) {
                this.error = 'Please enter a site name';
                console.log('Site name is empty');
                return;
            }

            this.submitting = true;
            this.error = '';
            this.errors = {};

            try {
                console.log('Submitting site with name:', this.site_name);
                const res = await axios.post('/api/v1/sites', {
                    site_name: this.site_name.trim()
                });

                console.log('Create response:', res.data);

                if (res.data.success) {
                    console.log('Site created successfully, redirecting...');
                    // Redirect to sites list
                    setTimeout(() => {
                        window.location.href = '/pwa/sites';
                    }, 500);
                } else {
                    this.error = res.data.message || 'Failed to create site';
                    console.log('Error from API:', this.error);
                }
            } catch (err) {
                console.error('Create error:', err);
                
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors || {};
                    this.error = 'Validation failed';
                    console.log('Validation errors:', this.errors);
                } else {
                    this.error = err.response?.data?.message || 'Failed to create site. Please try again.';
                }
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection