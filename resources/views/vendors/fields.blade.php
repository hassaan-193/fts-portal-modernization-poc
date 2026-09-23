<div class="row">
    <div class="col-md-3 col-sm-6">
<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', __('models/vendors.fields.name').':') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>
</div>

    <div class="col-md-3 col-sm-6">


<!-- Email Field (primary) -->
<div class="form-group">
    {!! Form::label('email', __('models/vendors.fields.email').':') !!}
    {!! Form::email('email', null, ['class' => ($errors->has('email')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('email'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
    <small class="form-text text-muted">Primary address</small>
</div>
</div>

    <div class="col-md-3 col-sm-6">
<!-- Contact Person Field -->
<div class="form-group">
    {!! Form::label('contact_person', __('models/vendors.fields.contact_person').':') !!}
    {!! Form::text('contact_person', null, ['class' => ($errors->has('contact_person')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('contact_person'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('contact_person') }}</strong>
        </span>
    @endif
</div>
</div>

    <div class="col-md-3 col-sm-6">


<!-- Contact No Field -->
<div class="form-group">
    {!! Form::label('contact_no', __('models/vendors.fields.contact_no').':') !!}
    {!! Form::text('contact_no', null, ['class' => ($errors->has('contact_no')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('contact_no'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('contact_no') }}</strong>
        </span>
    @endif
</div>

</div>
</div>

<!-- Additional Emails - every address here is mailed alongside the primary one -->
@php
    $additionalEmails = old('emails', isset($vendor) ? $vendor->additionalEmails() : []);
    $additionalEmails = array_values(array_filter(is_array($additionalEmails) ? $additionalEmails : [], fn($e) => trim((string) $e) !== ''));
@endphp
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            {!! Form::label('emails', 'Additional Emails:') !!}
            <div id="additional_emails_wrapper">
                @foreach($additionalEmails as $additionalEmail)
                    <div class="input-group mb-2 additional-email-row">
                        <input type="email" name="emails[]" class="form-control" value="{{ $additionalEmail }}" placeholder="name@example.com">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger remove-email-row" title="Remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add_email_row">+ Add Email</button>
            @if ($errors->has('emails.*'))
                <span class="d-block text-danger"><strong>{{ $errors->first('emails.*') }}</strong></span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-sm-6">

<!-- Contact No Two Field -->
<div class="form-group">
    {!! Form::label('contact_no_two', __('models/vendors.fields.contact_no_two').':') !!}
    {!! Form::text('contact_no_two', null, ['class' => ($errors->has('contact_no_two')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('contact_no_two'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('contact_no_two') }}</strong>
        </span>
    @endif
</div>

</div>

<div class="col-md-3 col-sm-6">
    <!-- Vat No Field -->
    <div class="form-group">
        {!! Form::label('vat_no', __('models/vendors.fields.vat_no').':') !!}
        {!! Form::text('vat_no', null, ['class' => ($errors->has('vat_no')) ? 'form-control is-invalid' : 'form-control']) !!}
        @if ($errors->has('vat_no'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('vat_no') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="col-md-3 col-sm-6">
    <!-- Payment Terms Field -->
    <div class="form-group">
        {!! Form::label('payment_terms', __('models/vendors.fields.payment_terms').':') !!}
        {!! Form::text('payment_terms', null, ['class' => ($errors->has('payment_terms')) ? 'form-control is-invalid' : 'form-control']) !!}
        @if ($errors->has('payment_terms'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('payment_terms') }}</strong>
            </span>
        @endif
    </div>
</div>
</div>

<!-- Terms and Conditions Field -->
<div class="row mt-3">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            {!! Form::label('terms_and_conditions', 'Terms & Conditions:') !!}
            {!! Form::textarea('terms_and_conditions', null, ['class' => ($errors->has('terms_and_conditions')) ? 'form-control is-invalid summernote' : 'form-control summernote', 'rows' => 5, 'placeholder' => 'Enter vendor-specific terms and conditions (optional - leave empty to use default)']) !!}
            @if ($errors->has('terms_and_conditions'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('terms_and_conditions') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">Leave empty to use default terms when creating LPOUT/PO</small>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(function () {
            $('.summernote').summernote({
                height: 250
            })
        })
    </script>
@endsection

<div class="row mt-3">

<div class="col-md-3 col-sm-6">
    <!-- Credit Limit Field -->
    <div class="form-group">
        {!! Form::label('credit_limit', __('models/vendors.fields.credit_limit').':') !!}
        {!! Form::text('credit_limit', null, ['class' => ($errors->has('credit_limit')) ? 'form-control is-invalid' : 'form-control']) !!}
        @if ($errors->has('credit_limit'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('credit_limit') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="col-md-3 col-sm-6">
    <!-- Location Field -->
    <div class="form-group">
        {!! Form::label('location', __('models/vendors.fields.location').':') !!}
        {!! Form::text('location', null, ['class' => 'form-control']) !!}
    </div>
</div>
    <div class="col-md-4 col-sm-6">
        <!-- Vendor Specialization Field -->
        <div class="form-group">
            {!! Form::label('vendor_specialization', __('models/vendors.fields.vendor_specialization').':') !!}
            {!! Form::text('vendor_specialization', null, ['class' => ($errors->has('vendor_specialization')) ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'e.g. Electronics, Machinery, etc.']) !!}
            @if ($errors->has('vendor_specialization'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('vendor_specialization') }}</strong>
                </span>
            @endif
        </div>
    </div>

<div class="col-md-3 col-sm-6">
<!-- File Field -->
<div class="form-group pt-1">
            <div class="custom-file mt-4">
        {!! Form::file('file',['class' => 'custom-file-input']) !!}
        {!! Form::label('file', __('models/vendors.fields.file').':' , ['class' => 'custom-file-label']) !!}
    </div>
</div>
</div>
</div>

<!-- Payment Preference Section -->
<div class="row mt-4">
    <div class="col-md-12">
        <h4 class="text-danger">@lang('models/vendors.fields.payment_preference')</h4>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- Payment Preference Field -->
        <div class="form-group">
            {!! Form::label('payment_preference', __('models/vendors.fields.payment_preference').':') !!}
            {!! Form::select('payment_preference', ['cod' => 'Cash on Delivery', 'pdc' => 'PDC (Post Dated Check)'], null, ['class' => ($errors->has('payment_preference')) ? 'form-control is-invalid' : 'form-control', 'id' => 'payment_preference']) !!}
            @if ($errors->has('payment_preference'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('payment_preference') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6" id="pdc_days_section" style="display: none;">
        <!-- PDC Number of Days Field -->
        <div class="form-group">
            {!! Form::label('pdc_number_of_days', __('models/vendors.fields.pdc_number_of_days').':') !!}
            {!! Form::number('pdc_number_of_days', null, ['class' => ($errors->has('pdc_number_of_days')) ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'Number of days']) !!}
            @if ($errors->has('pdc_number_of_days'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('pdc_number_of_days') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6" id="pdc_payment_option_section" style="display: none;">
        <!-- PDC Payment Option Field -->
        <div class="form-group">
            {!! Form::label('pdc_payment_option', __('models/vendors.fields.pdc_payment_option').':') !!}
            {!! Form::select('pdc_payment_option', ['on_delivery_amount' => 'On Delivery', 'on_payment_release_amount' => 'On PO Release '], null, ['class' => ($errors->has('pdc_payment_option')) ? 'form-control is-invalid' : 'form-control', 'id' => 'pdc_payment_option']) !!}
            @if ($errors->has('pdc_payment_option'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('pdc_payment_option') }}</strong>
                </span>
            @endif
        </div>
    </div>




@section('scripts')
@parent
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();

            // --- Additional vendor emails (repeatable rows) ---
            function buildEmailRow() {
                return '<div class="input-group mb-2 additional-email-row">' +
                       '<input type="email" name="emails[]" class="form-control" placeholder="name@example.com">' +
                       '<div class="input-group-append">' +
                       '<button type="button" class="btn btn-outline-danger remove-email-row" title="Remove"><i class="fa fa-times"></i></button>' +
                       '</div></div>';
            }

            $('#add_email_row').on('click', function () {
                $('#additional_emails_wrapper').append(buildEmailRow());
            });

            $(document).on('click', '.remove-email-row', function () {
                $(this).closest('.additional-email-row').remove();
            });

            // Handle payment preference change
            const togglePaymentOptions = () => {
                const paymentPref = $('#payment_preference').val();
                if (paymentPref === 'pdc') {
                    $('#pdc_days_section').show();
                    $('#pdc_payment_option_section').show();
                    $('#pdc_number_of_days').prop('required', true);
                    $('#pdc_payment_option').prop('required', true);
                } else {
                    $('#pdc_days_section').hide();
                    $('#pdc_payment_option_section').hide();
                    $('#pdc_number_of_days').prop('required', false).val('');
                    $('#pdc_payment_option').prop('required', false).val('');
                }
            };

            // Initialize on page load
            togglePaymentOptions();

            // Listen for changes
            $('#payment_preference').on('change', togglePaymentOptions);
        });
    </script>
@endsection
<div class="row">
    <div class="col-sm-12">

<!-- Submit Field -->
<div class="form-group">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('vendors.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
</div>
</div>
