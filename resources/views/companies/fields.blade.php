<div class="row">
    <div class="col-md-3 col-sm-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('name', __('models/companies.fields.name').':') !!}
            {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Email Field -->
        <div class="form-group">
            {!! Form::label('email', __('models/companies.fields.email').':') !!}
            {!! Form::email('email', null, ['class' => ($errors->has('email')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('email'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Contact Person Field -->
        <div class="form-group">
            {!! Form::label('contact_person', __('models/companies.fields.contact_person').':') !!}
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
            {!! Form::label('contact_no', __('models/companies.fields.contact_no').':') !!}
            {!! Form::text('contact_no', null, ['class' => ($errors->has('contact_no')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('contact_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('contact_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">

        <!-- Contact No Two Field -->
        <div class="form-group">
            {!! Form::label('contact_no_two', __('models/companies.fields.contact_no_two').':') !!}
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
            {!! Form::label('vat_no', __('models/companies.fields.vat_no').':') !!}
            {!! Form::text('vat_no', null, ['class' => ($errors->has('vat_no')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('vat_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('vat_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Location Field -->
        <div class="form-group">
            {!! Form::label('location', __('models/companies.fields.location').':') !!}
            <!-- {!! Form::textarea('location', null, ['class' => 'form-control']) !!} -->
            {!! Form::text('location', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- File Field -->
        <div class="form-group pt-1">
            <div class="custom-file mt-4">
                {!! Form::file('file',['class' => 'custom-file-input']) !!}
                {!! Form::label('file', __('models/companies.fields.file').':' , ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>
@section('scripts')
@parent
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection

    <div class="col-md-3 col-sm-6">
        <!-- Billing Address Field -->
        <div class="form-group">
            {!! Form::label('billing_address', __('models/companies.fields.billing_address').':') !!}
            {!! Form::text('billing_address', null, ['class' => ($errors->has('billing_address')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('billing_address'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('billing_address') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Billing Contact Person Field -->
        <div class="form-group">
            {!! Form::label('billing_contact_person', __('models/companies.fields.billing_contact_person').':') !!}
            {!! Form::text('billing_contact_person', null, ['class' => ($errors->has('billing_contact_person')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('billing_contact_person'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('billing_contact_person') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Billing Pob Field -->
        <div class="form-group">
            {!! Form::label('billing_pob', __('models/companies.fields.billing_pob').':') !!}
            {!! Form::text('billing_pob', null, ['class' => ($errors->has('billing_pob')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('billing_pob'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('billing_pob') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Billing Email Field -->
        <div class="form-group">
            {!! Form::label('billing_email', __('models/companies.fields.billing_email').':') !!}
            {!! Form::text('billing_email', null, ['class' => ($errors->has('billing_email')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('billing_email'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('billing_email') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Shipping Address Field -->
        <div class="form-group">
            {!! Form::label('shipping_address', __('models/companies.fields.shipping_address').':') !!}
            {!! Form::text('shipping_address', null, ['class' => ($errors->has('shipping_address')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('shipping_address'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('shipping_address') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Shipping Contact Person Field -->
        <div class="form-group">
            {!! Form::label('shipping_contact_person', __('models/companies.fields.shipping_contact_person').':') !!}
            {!! Form::text('shipping_contact_person', null, ['class' => ($errors->has('shipping_contact_person')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('shipping_contact_person'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('shipping_contact_person') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Shipping Pob Field -->
        <div class="form-group">
            {!! Form::label('shipping_pob', __('models/companies.fields.shipping_pob').':') !!}
            {!! Form::text('shipping_pob', null, ['class' => ($errors->has('shipping_pob')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('shipping_pob'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('shipping_pob') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Shipping Email Field -->
        <div class="form-group">
            {!! Form::label('shipping_email', __('models/companies.fields.shipping_email').':') !!}
            {!! Form::text('shipping_email', null, ['class' => ($errors->has('shipping_email')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('shipping_email'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('shipping_email') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Payment Terms Field -->
        <div class="form-group">
            {!! Form::label('payment_terms', __('models/companies.fields.payment_terms').':') !!}
            {!! Form::text('payment_terms', null, ['class' => ($errors->has('payment_terms')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('payment_terms'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('payment_terms') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Credit Limit Field -->
        <div class="form-group">
            {!! Form::label('credit_limit', __('models/companies.fields.credit_limit').':') !!}
            {!! Form::text('credit_limit', null, ['class' => ($errors->has('credit_limit')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('credit_limit'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('credit_limit') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('companies.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
        </div>
    </div>
</div>
