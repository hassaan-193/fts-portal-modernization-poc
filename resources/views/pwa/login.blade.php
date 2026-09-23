@extends('pwa.app')

@section('styles')
<style>
    .login-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .login-box {
        background: white;
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 380px;
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .logo {
        width: auto;
        height: auto;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin: 0 auto 16px;
    }

    .login-header h1 {
        font-size: 24px;
        color: #2c3e50;
        margin: 0 0 4px 0;
    }

    .login-header p {
        font-size: 13px;
        color: #666;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group input:disabled {
        background: #f5f5f5;
        color: #999;
    }

    .error-alert {
        background: #FEE;
        border-left: 4px solid #FF6B5B;
        color: #c33;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-login:active:not(:disabled) {
        transform: scale(0.98);
    }

    .btn-login:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endsection

@section('content')
<div class="login-container" x-data="loginForm()">
    <div class="login-box">
        <div class="login-header">
            <div class="logo">
                <img src="{{ asset('dist/img/logo.png') }}" width="150" />
            </div>
        </div>

        <form @submit.prevent="submit()">
            <!-- EMAIL INPUT -->
            <div class="form-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    x-model="email"
                    placeholder="Enter your email"
                    required
                    @keydown="loading = false"
                >
            </div>

            <!-- PASSWORD INPUT -->
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    x-model="password"
                    placeholder="Enter your password"
                    required
                    @keydown="loading = false"
                >
            </div>

            <!-- ERROR ALERT -->
            <div x-show="error" class="error-alert">
                <span x-text="error"></span>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit" class="btn-login" @click="submit()">
                <span x-show="!loading">Sign In</span>
                <span x-show="loading">
                    <span class="spinner"></span>Signing In...
                </span>
            </button>
        </form>
    </div>
</div>

<script>
function loginForm() {
    return {
        email: '',
        password: '',
        loading: false,
        error: '',

        async submit() {
            // Validation
            if (!this.email || !this.password) {
                this.error = 'Please enter email and password';
                return;
            }

            this.loading = true;
            this.error = '';

            try {
                const response = await axios.post('/api/v1/foreman/login', {
                    email: this.email,
                    password: this.password,
                });

                console.log('Login response:', response.data);

                if (response.data.success) {
                    // Store token in localStorage
                    localStorage.setItem('foreman_token', response.data.token);
                    
                    // Store user info
                    localStorage.setItem('foreman_user', JSON.stringify(response.data.user));
                    
                    // Configure axios default header for future requests
                    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                    
                    // Get user roles and redirect accordingly
                    const roles = response.data.user.roles || [];
                    console.log('User roles:', roles);
                    
                    let redirectUrl = '/pwa/home'; // Default redirect
                    
                    if (roles.includes('qr_scanner')) {
                        redirectUrl = '/pwa/qr-scanner';
                    }
                    else if (roles.includes('engineer')) {
                        redirectUrl = '/pwa/sites';
                    }
                    else if (roles.includes('foreman')) {
                        redirectUrl = '/pwa/home';
                    }
                    else if (roles.includes('manager')) {
                        redirectUrl = '/pwa/home';
                    }
                    
                    console.log('Redirecting to:', redirectUrl);
                    
                    // Redirect after 500ms
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 500);
                } else {
                    this.error = response.data.message || 'Login failed';
                }
            } catch (error) {
                console.error('Login error:', error);
                
                if (error.response?.status === 401) {
                    this.error = 'Invalid email or password';
                } else if (error.response?.status === 403) {
                    this.error = 'You are not authorized';
                } else if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const firstError = Object.values(errors)[0];
                    this.error = Array.isArray(firstError) ? firstError[0] : firstError;
                } else {
                    this.error = error.response?.data?.message || 'Login failed. Please try again.';
                }
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endsection