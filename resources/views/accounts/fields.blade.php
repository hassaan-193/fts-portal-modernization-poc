<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', __('models/accounts.fields.type').':') !!}
    {!! Form::text('type', null, ['class' => ($errors->has('type')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('type'))
        <span class="invalid-feedback">
            <strong>The name field is required.</strong>
        </span>
    @endif
</div>
<!-- Code Field -->
<div class="form-group col-sm-6">
    {!! Form::label('code', __('models/accounts.fields.code').':') !!}
    {!! Form::text('code', null, ['class' => ($errors->has('code')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('code'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('code') }}</strong>
        </span>
    @endif
</div>
<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('account_type', 'Account Type:') !!}
    {!! Form::select('account_type', [
        '' => '',
        'Debit' => 'Debit',
        'Credit' => 'Credit',
        'Asset' => [
            'Other Asset' => 'Other Asset',
            'Other Current Asset' => 'Other Current Asset',
            'Cash' => 'Cash',
            'Bank' => 'Bank',
            'Fixed Asset' => 'Fixed Asset',
            'Accounts Receivable' => 'Accounts Receivable',
            'Stock' => 'Stock',
            'Payment Clearing Account' => 'Payment Clearing Account',
            'Intangible Asset' => 'Intangible Asset',
            'Long Term Asset' => 'Long Term Asset',
            'Deferred Tax Asset' => 'Deferred Tax Asset',
            'Imprest Account' => 'Imprest Account',
        ],
        'Liability' => [
            'Other Current Liability' => 'Other Current Liability',
            'Credit Card' => 'Credit Card',
            'Long Term Liability' => 'Long Term Liability',
            'Other Liability' => 'Other Liability',
            'Accounts Payable' => 'Accounts Payable',
            'Deferred Tax Liability' => 'Deferred Tax Liability',
        ],
        'Equity' => [
            'Equity' => 'Equity',
        ],
        'Income' => [
            'Income' => 'Income',
            'Other Income' => 'Other Income',
        ],
        'Expense' => [
            'Expense' => 'Expense',
            'Cost Of Goods Sold' => 'Cost Of Goods Sold',
            'Other Expense' => 'Other Expense',
        ],
    ], null, [
        'id' => 'account_type',
        'class' => $errors->has('account_type') ? 'form-control is-invalid' : 'form-control'
    ]) !!}

    @if ($errors->has('account_type'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('account_type') }}</strong>
        </span>
    @endif
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('accounts.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $('#account_type').select2({
            theme: 'bootstrap4',
            placeholder: "Select Account Type",
            allowClear: true
        });
    </script>
@endsection
