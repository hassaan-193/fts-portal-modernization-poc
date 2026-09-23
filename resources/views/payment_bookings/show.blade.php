@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">
                        @lang('crud.detail') @lang('models/payment_bookings.singular')
                        <span class="badge badge-light border ml-2">{{ $paymentBooking->reference_no }}</span>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('payment-bookings.index') !!}">@lang('models/payment_bookings.title')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">@lang('models/payment_bookings.singular') details</h3>
                        <div class="card-tools">
                            @include('payment_bookings.status_badge', ['booking' => $paymentBooking])
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-unbordered mb-3">
                            @include('payment_bookings.show_fields')
                        </ul>

                        <div class="mt-3">
                            @if($paymentBooking->isEditable() && (int) $paymentBooking->created_by === (int) auth()->id())
                                <a href="{{ route('payment-bookings.edit', $paymentBooking->id) }}" class="btn btn-info mr-1">
                                    <i class="fa fa-edit"></i> @lang('crud.edit')
                                </a>
                            @endif

                            @if(in_array($paymentBooking->status, [\App\Models\PaymentBooking::STATUS_DRAFT, \App\Models\PaymentBooking::STATUS_REJECTED], true)
                                && (int) $paymentBooking->created_by === (int) auth()->id())
                                <a href="{{ route('payment-bookings.submit', $paymentBooking->id) }}" class="btn btn-warning mr-1"
                                   onclick="return confirm('Submit this booking for review?')">
                                    <i class="fa fa-paper-plane"></i> Submit for review
                                </a>
                            @endif

                            @if($paymentBooking->status === \App\Models\PaymentBooking::STATUS_ON_HOLD
                                && !empty(\App\Services\PaymentBookingService::levelsForUser()))
                                <a href="{{ route('payment-bookings.resume', $paymentBooking->id) }}" class="btn btn-secondary mr-1"
                                   onclick="return confirm('Put this booking back in the review queue?')">
                                    <i class="fa fa-play"></i> Resume review
                                </a>
                            @endif

                            <a href="{{ route('payment-bookings.index') }}" class="btn btn-outline-danger text-maroon">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                {{-- ========== Approval chain ========== --}}
                <div class="bg-white card-primary card-maroon mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Approval chain</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Both levels must approve in order before this booking counts as approved.
                        </p>
                        <ul class="list-group list-group-flush">
                            @foreach($chain as $step)
                                @php
                                    $stateClass = [
                                        'Approved'    => 'success',
                                        'Rejected'    => 'danger',
                                        'On Hold'     => 'dark',
                                        'Pending'     => 'warning',
                                        'Waiting'     => 'secondary',
                                        'Not reached' => 'light',
                                    ][$step['state']] ?? 'secondary';
                                @endphp
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>Level {{ $step['level'] }} — {{ $step['level_label'] }}</strong><br>
                                            <small class="text-muted">{{ $step['role'] }}</small>
                                            @if($step['decided_by'])
                                                <br><small><i class="fa fa-user mr-1"></i>{{ $step['decided_by'] }}</small>
                                            @endif
                                            @if($step['decided_at'])
                                                <br><small class="text-muted">{{ $step['decided_at'] }}</small>
                                            @endif
                                            @if($step['note'])
                                                <br><small class="text-muted"><em>“{{ $step['note'] }}”</em></small>
                                            @endif
                                        </div>
                                        <span class="badge badge-{{ $stateClass }} {{ $stateClass === 'light' ? 'border' : '' }}">
                                            {{ $step['state'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- ========== Decision form ========== --}}
                @if($canDecide)
                    <div class="bg-white card-primary card-maroon">
                        <div class="card-header">
                            <h3 class="card-title">Your review</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                You are acting at level
                                {{ $paymentBooking->pendingLevel() }} —
                                {{ \App\Models\PaymentBookingApproval::levels()[$paymentBooking->pendingLevel()] }}.
                                A note is required to reject or hold.
                            </p>

                            <div class="form-group">
                                <label for="decision_note">@lang('models/payment_bookings.fields.note')</label>
                                <textarea id="decision_note" class="form-control" rows="3"
                                          placeholder="Optional for approve, required for reject or hold"></textarea>
                            </div>

                            <div class="d-flex">
                                @foreach([
                                    'approve' => ['label' => 'Approve', 'class' => 'success', 'icon' => 'check'],
                                    'hold'    => ['label' => 'Hold',    'class' => 'secondary', 'icon' => 'pause'],
                                    'reject'  => ['label' => 'Reject',  'class' => 'danger', 'icon' => 'times'],
                                ] as $action => $meta)
                                    {!! Form::open([
                                        'route' => ['payment-bookings.' . $action, $paymentBooking->id],
                                        'method' => 'post',
                                        'class' => 'mr-1 decision-form',
                                        'data-action' => $action,
                                    ]) !!}
                                        <input type="hidden" name="note" class="decision-note-input">
                                        <button type="submit" class="btn btn-{{ $meta['class'] }}">
                                            <i class="fa fa-{{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                                        </button>
                                    {!! Form::close() !!}
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
    // One shared note box feeds whichever decision button is pressed, so the
    // reviewer never has to retype it after picking a different action.
    $('.decision-form').on('submit', function () {
        var action = $(this).data('action');
        var note = $.trim($('#decision_note').val());

        if ((action === 'reject' || action === 'hold') && note === '') {
            alert('Please say why this booking is being ' + (action === 'reject' ? 'rejected' : 'put on hold') + '.');
            return false;
        }

        $(this).find('.decision-note-input').val(note);

        return confirm('Record this decision?');
    });
});
</script>
@endsection
