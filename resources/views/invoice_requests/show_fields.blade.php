<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- User Id Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoice_requests.fields.user_id')</b> <a class="float-right">{{ $invoiceRequest->user->name }}</a>
        </li>
        <!-- Note Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoice_requests.fields.note')</b> <a class="float-right">{{ $invoiceRequest->note }}</a>
        </li>
    </div>
    <!-- Payment Terms Field -->
    <div class="col-md-4 col-sm-6">
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.payment_terms')</b> <a class="float-right">{{ $invoiceRequest->payment_terms }}</a>
        </li>
    </div>
     <!-- Delivery Date Field -->
     <div class="col-md-4 col-sm-6">
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.delivery_date')</b> <a class="float-right">{{ $invoiceRequest->delivery_date }}</a>
        </li>
    </div>
</div>

