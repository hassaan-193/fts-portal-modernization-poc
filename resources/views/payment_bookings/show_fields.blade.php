<div class="row">
    <div class="col-md-4 col-sm-6">
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.reference_no')</b> <br>
            <a class="text-left">{{ $paymentBooking->reference_no }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.booking_type')</b> <br>
            <a class="text-left">{{ $paymentBooking->type_label }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>{{ $paymentBooking->isCash() ? __('models/payment_bookings.fields.paid_to') : __('models/payment_bookings.fields.payee') }}</b> <br>
            <a class="text-left">{{ $paymentBooking->payee }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.amount')</b> <br>
            <a class="text-left">AED {{ number_format($paymentBooking->amount, 2) }}</a>
        </li>
    </div>

    <div class="col-md-4 col-sm-6">
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.payment_against')</b> <br>
            <a class="text-left">{{ $paymentBooking->payment_against ?: '—' }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.project_cost_centre')</b> <br>
            <a class="text-left">{{ $paymentBooking->project_cost_centre ?: '—' }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.purpose')</b> <br>
            <a class="text-left">{{ $paymentBooking->purpose ?: '—' }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.booking_date')</b> <br>
            <a class="text-left">{{ $paymentBooking->booking_date ? $paymentBooking->booking_date->format('d M Y') : '—' }}</a>
        </li>
    </div>

    <div class="col-md-4 col-sm-6">
        @if($paymentBooking->isCheque())
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.cheque_number')</b> <br>
                <a class="text-left">{{ $paymentBooking->cheque_number ?: '—' }}</a>
            </li>

            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.bank_account')</b> <br>
                <a class="text-left">{{ $paymentBooking->bank_account ?: '—' }}</a>
            </li>

            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.cheque_date')</b> <br>
                <a class="text-left">{{ $paymentBooking->cheque_date ? $paymentBooking->cheque_date->format('d M Y') : '—' }}</a>
            </li>

            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.release_date')</b> <br>
                <a class="text-left">{{ $paymentBooking->release_date ? $paymentBooking->release_date->format('d M Y') : '—' }}</a>
            </li>
        @else
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.cash_account')</b> <br>
                <a class="text-left">{{ $paymentBooking->cash_account ?: '—' }}</a>
            </li>

            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/payment_bookings.fields.payment_date')</b> <br>
                <a class="text-left">{{ $paymentBooking->payment_date ? $paymentBooking->payment_date->format('d M Y') : '—' }}</a>
            </li>
        @endif

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.requested_by')</b> <br>
            <a class="text-left">{{ optional($paymentBooking->creator)->name ?? '—' }}</a>
        </li>

        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/payment_bookings.fields.released')</b> <br>
            <a class="text-left">
                AED {{ number_format($paymentBooking->released_amount, 2) }}
                @unless($paymentBooking->is_released)
                    <small class="text-muted">(not yet released)</small>
                @endunless
            </a>
        </li>
    </div>
</div>
