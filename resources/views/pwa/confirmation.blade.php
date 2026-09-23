@extends('pwa.app')

@section('styles')
<style>
    .confirmation-container {
        display:  flex;
        flex-direction:  column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 20px;
        padding-bottom: 80px;
        text-align: center;
    }

    /* ============================================ */
    /* SUCCESS ICON                                 */
    /* ============================================ */

    .success-icon {
        margin-bottom: 24px;
        animation: slideInDown 0.6s ease-out;
    }

    .icon-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(255, 107, 91, 0.3);
        margin: 0 auto;
        font-size: 48px;
        color: white;
        font-weight: bold;
        animation: scaleIn 0.6s 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) both;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform:  translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ============================================ */
    /* MESSAGE SECTION                              */
    /* ============================================ */

    .message-section {
        margin-bottom: 32px;
    }

    .message-section h1 {
        font-size:   24px;
        font-weight:  700;
        color: #2c3e50;
        margin:   0 0 8px 0;
    }

    .message-section p {
        font-size: 14px;
        color: #666;
        margin: 0;
    }

    /* ============================================ */
    /* DETAILS CARD                                 */
    /* ============================================ */

    .details-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        width: 100%;
        max-width: 340px;
        box-shadow:   0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
    }

    .detail-label {
        font-size: 13px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    .detail-value {
        font-size: 14px;
        font-weight:   600;
        color: #2c3e50;
    }

    .detail-value.count {
        background: #FF6B5B;
        color: white;
        padding: 4px 12px;
        border-radius:   16px;
        font-size:   15px;
    }

    .detail-value.pending {
        color: #FF6B5B;
        font-weight:  600;
    }

    .divider {
        height: 1px;
        background: #eee;
        margin: 0;
    }

    /* ============================================ */
    /* INFO BOX                                     */
    /* ============================================ */

    .info-box {
        background:  #FFF5F3;
        border-left: 4px solid #FF6B5B;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom:  24px;
        width: 100%;
        max-width: 340px;
        font-size: 13px;
        color: #666;
        line-height: 1.5;
    }

    /* ============================================ */
    /* ACTION BUTTONS                               */
    /* ============================================ */

    .action-buttons {
        display:  flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        max-width: 340px;
    }

    .btn-action {
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size:   14px;
        font-weight:  600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #FF6B5B 0%, #FF8569 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 107, 91, 0.25);
    }

    .btn-primary:active {
        transform: scale(0.98);
        box-shadow: 0 2px 6px rgba(255, 107, 91, 0.15);
    }

    .btn-secondary {
        background: white;
        color: #FF6B5B;
        border:   2px solid #FF6B5B;
    }

    .btn-secondary:active {
        background: #FFF5F3;
    }
</style>
@endsection

@section('content')
<div class="confirmation-container" x-data="confirmation()" x-init="init()">
    
    <!-- ============================================ -->
    <!-- SUCCESS ICON                                -->
    <!-- ============================================ -->
    <div class="success-icon">
        <div class="icon-circle">✓</div>
    </div>

    <!-- ============================================ -->
    <!-- MESSAGE SECTION                             -->
    <!-- ============================================ -->
    <div class="message-section">
        <h1>Attendance Submitted</h1>
        <p>Your attendance has been successfully submitted</p>
    </div>

    <!-- ============================================ -->
    <!-- DETAILS CARD                                -->
    <!-- ============================================ -->
    <div class="details-card">
        <div class="detail-item">
            <span class="detail-label">Date</span>
            <span class="detail-value" x-text="formattedDate"></span>
        </div>
        <div class="divider"></div>
        <div class="detail-item">
            <span class="detail-label">Absent Labors Marked</span>
            <span class="detail-value count" x-text="count"></span>
        </div>
        <div class="divider"></div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value pending">Pending Review</span>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- INFO BOX                                    -->
    <!-- ============================================ -->
    <div class="info-box">
        <p>Your manager will review and approve the attendance records shortly.</p>
    </div>

    <!-- ============================================ -->
    <!-- ACTION BUTTONS                              -->
    <!-- ============================================ -->
    <div class="action-buttons">
        <button class="btn-action btn-primary" @click="goHome()">
            Back to Home
        </button>

    </div>

</div>

<script>
function confirmation() {
    return {
        date: '',
        count: 0,

        init() {
            const params = new URLSearchParams(window.location.search);
            this.date = params.get('date') || new Date().toISOString().split('T')[0];
            this.count = parseInt(params.get('count')) || 0;
        },

        get formattedDate() {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            };
            return new Date(this.date).toLocaleDateString('en-US', options);
        },

        goHome() {
            localStorage.removeItem('selectedAbsentLabors');
            window.location.href = '/pwa/home';
        }


    };
}
</script>
@endsection