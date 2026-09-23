@php
    // Yajra hands this view the row's attributes, so the buttons are decided from
    // $status / $created_by directly rather than re-loading the model per row.
    $isMine     = (int) $created_by === (int) auth()->id();
    $isEditable = in_array((int) $status, [
        \App\Models\PaymentBooking::STATUS_DRAFT,
        \App\Models\PaymentBooking::STATUS_PENDING,
        \App\Models\PaymentBooking::STATUS_REJECTED,
    ], true);
    $isSubmittable = in_array((int) $status, [
        \App\Models\PaymentBooking::STATUS_DRAFT,
        \App\Models\PaymentBooking::STATUS_REJECTED,
    ], true);
@endphp
<div class='btn-group'>
    <a href="{{ route('payment-bookings.show', $id) }}" class='btn btn-success' title="View">
        <i class="fa fa-eye"></i>
    </a>

    @if($isMine && $isEditable)
        <a href="{{ route('payment-bookings.edit', $id) }}" class='btn btn-info' title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    @endif

    @if($isMine && $isSubmittable)
        <a href="{{ route('payment-bookings.submit', $id) }}" class='btn btn-warning' title="Submit for review"
           onclick="return confirm('Submit this booking for review?')">
            <i class="fa fa-paper-plane"></i>
        </a>
    @endif

    @if($isMine && (int) $status !== \App\Models\PaymentBooking::STATUS_APPROVED)
        {!! Form::open(['route' => ['payment-bookings.destroy', $id], 'method' => 'delete', 'style' => 'display:inline']) !!}
        {!! Form::button('<i class="fa fa-trash"></i>', [
            'type' => 'submit',
            'class' => 'btn btn-danger',
            'title' => 'Delete',
            'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
    @endif
</div>
