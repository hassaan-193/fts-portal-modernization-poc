@extends('pwa.app')

@section('styles')
<style>
    .offline-container {
        display:  flex;
        flex-direction:  column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        color: white;
        text-align: center;
    }

    /* ============================================ */
    /* OFFLINE ICON                                 */
    /* ============================================ */

    .offline-icon {
        font-size: 80px;
        margin-bottom:   24px;
        animation:  pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(0.95);
        }
    }

    /* ============================================ */
    /* OFFLINE CONTENT                              */
    /* ============================================ */

    .offline-content h1 {
        font-size:  28px;
        margin-bottom:   12px;
        font-weight: 700;
    }

    .offline-content p {
        font-size: 16px;
        margin-bottom:  24px;
        opacity: 0.9;
        line-height: 1.6;
    }

    /* ============================================ */
    /* OFFLINE TIPS                                 */
    /* ============================================ */

    .offline-tips {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 20px;
        border-radius:   12px;
        margin-bottom: 24px;
        max-width: 400px;
        text-align: left;
    }

    .offline-tips h3 {
        font-size:   14px;
        margin-bottom:   12px;
        font-weight:  600;
    }

    .offline-tips ul {
        list-style:   none;
        padding:  0;
        font-size:  13px;
    }

    .offline-tips li {
        padding: 4px 0;
        opacity: 0.9;
    }

    .offline-tips li:before {
        content: "✓ ";
        margin-right: 8px;
    }

    /* ============================================ */
    /* RETRY BUTTON                                 */
    /* ============================================ */

    .retry-btn {
        background: white;
        color: #667eea;
        border:  none;
        padding: 12px 32px;
        border-radius:   8px;
        font-size:  14px;
        font-weight: 600;
        cursor: pointer;
        transition:   all 0.2s ease;
    }

    .retry-btn:active {
        transform: scale(0.98);
    }
</style>
@endsection

@section('content')
<div class="offline-container">
    
    <!-- ============================================ -->
    <!-- OFFLINE ICON                                -->
    <!-- ============================================ -->
    <div class="offline-icon">
        📡
    </div>
    
    <!-- ============================================ -->
    <!-- OFFLINE CONTENT                             -->
    <!-- ============================================ -->
    <div class="offline-content">
        <h1>You're Offline</h1>
        <p>No internet connection detected.Please check your connection and try again.</p>
    </div>

    <!-- ============================================ -->
    <!-- OFFLINE TIPS                                -->
    <!-- ============================================ -->
    <div class="offline-tips">
        <h3>Tips: </h3>
        <ul>
            <li>Check your mobile data/WiFi</li>
            <li>Try moving to a different location</li>
            <li>Restart your airplane mode</li>
            <li>Your data will sync when you reconnect</li>
        </ul>
    </div>

    <!-- ============================================ -->
    <!-- RETRY BUTTON                                -->
    <!-- ============================================ -->
    <button class="retry-btn" onclick="window.location.reload()">
        Retry Connection
    </button>

</div>
@endsection