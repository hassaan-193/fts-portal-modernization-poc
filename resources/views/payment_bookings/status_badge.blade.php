@php
    // One badge vocabulary for the list, the review queue and the detail screen.
    $map = [
        \App\Models\PaymentBooking::STATUS_DRAFT    => 'secondary',
        \App\Models\PaymentBooking::STATUS_PENDING  => 'warning',
        \App\Models\PaymentBooking::STATUS_VERIFIED => 'info',
        \App\Models\PaymentBooking::STATUS_APPROVED => 'success',
        \App\Models\PaymentBooking::STATUS_REJECTED => 'danger',
        \App\Models\PaymentBooking::STATUS_ON_HOLD  => 'dark',
    ];
    $class = $map[$booking->status] ?? 'secondary';
@endphp
<span class="badge badge-{{ $class }}">{{ $booking->status_label }}</span>
@if($booking->status === \App\Models\PaymentBooking::STATUS_APPROVED && !$booking->is_released)
    <span class="badge badge-light border">Pending release</span>
@endif
