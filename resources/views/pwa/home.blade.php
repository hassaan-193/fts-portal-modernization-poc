@extends('pwa.app')

@section('styles')
<style>
    .attendance-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: #f8f9fa;
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    }

    .header button {
        background: none;
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
        padding: 4px 8px;
        transition: color 0.3s;
    }

    .header button:hover {
        color: #ccc;
    }

    .home-menu {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 16px;
        gap: 16px;
    }

    .menu-card {
        background: white;
        border: 2px solid #FF6B5B;
        border-radius: 10px;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .menu-card:hover {
        background: #FFF9F7;
        transform: scale(1.02);
    }

    .menu-card .icon {
        font-size: 28px;
        color: #FF6B5B;
    }

    .menu-content {
        flex: 1;
        padding-left: 10px;
        text-align: left;
    }

    .menu-content h3 {
        margin: 0;
        font-size: 16px;
        color: #2c3e50;
        font-weight: bold;
    }

    .menu-content p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #666;
    }
</style>
@endsection

@section('content')
<div class="attendance-container" x-data="homeApp()">
    <!-- Header -->
    <div class="header">
        <img src="https://system.firetechnicalservices.com/dist/img/logo-top.png" height="60" width="160" alt="Fire Technical Services" class="brand-image">
        <button @click="logout()">➜]</button>
    </div>

    <!-- Home Menu -->
    <div class="home-menu">
        <div class="menu-card" onclick="window.location.href='/pwa/presents'">
            <div class="menu-content">
                <h3>Overtime</h3>
                <p>Mark Present For the Day</p>
            </div>
            <div class="icon">✅</div>
        </div>

        <div class="menu-card" onclick="window.location.href='/pwa/absents'">
            <div class="menu-content">
                <h3>Absents</h3>
                <p>Mark Absent For the Day</p>
            </div>
            <div class="icon">❌</div>
        </div>

        <div class="menu-card" onclick="window.location.href='/pwa/present-history'">
            <div class="menu-content">
                <h3>Overtime History</h3>
                <p>View Records of All Marked Presents</p>
            </div>
            <div class="icon">📖</div>
        </div>

        <div class="menu-card" onclick="window.location.href='/pwa/absent-history'">
            <div class="menu-content">
                <h3>Absent History</h3>
                <p>View Records of All Marked Absents</p>
            </div>
            <div class="icon">🕒</div>
        </div>
        <div class="menu-card" onclick="window.location.href='/pwa/sites'">
            <div class="menu-content">
                <h3>Add Sites</h3>
                <p>View All Sites</p>
            </div>
            <div class="icon">🕒</div>
        </div>

        <div class="menu-card" onclick="window.location.href='/pwa/qr-scanner'">
            <div class="menu-content">
                <h3>QR Attendance</h3>
                <p>Scan Staff QR for Check-in/Out</p>
            </div>
            <div class="icon">📷</div>
        </div>
    </div>
</div>

<script>
function homeApp() {
    return {
        logout() {
            if (confirm('Are you sure you want to log out?')) {
                localStorage.removeItem('foreman_token'); // Clear token (adjust if role-specific tokens exist)
                window.location.href = '/pwa/login'; // Redirect to the login page
            }
        }
    };
}
</script>
@endsection