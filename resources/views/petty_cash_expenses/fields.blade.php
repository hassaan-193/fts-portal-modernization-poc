<div class="row">
    <!-- name Field -->
    <div class="form-group col-md-6">
        {!! Form::label('name', __('models/petty_cash_expenses.fields.name').':') !!}
        {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control'])
        !!}
        @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>

    <!-- category Field -->
    <div class="form-group col-md-6">
        {!! Form::label('category', __('models/petty_cash_expenses.fields.category').':') !!}
        {!! Form::text('category', null, ['class' => ($errors->has('category')) ? 'form-control is-invalid' :
        'form-control']) !!}
        @if ($errors->has('category'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('category') }}</strong>
        </span>
        @endif
    </div>

    <!-- amount Field -->
    <div class="form-group col-md-6">
        {!! Form::label('amount', __('models/petty_cash_expenses.fields.amount').':') !!}
        {!! Form::number('amount', null, ['class' => ($errors->has('amount')) ? 'form-control is-invalid' :
        'form-control', 'step' => 0.1 ]) !!}
        @if ($errors->has('amount'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('amount') }}</strong>
        </span>
        @endif
    </div>

    <!-- Date Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('date', __('models/petty_cash_expenses.fields.date').':') !!}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                </span>
            </div>
            {!! Form::text('date', null, ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control'
            ,'id'=>'date']) !!}
            @if ($errors->has('date'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger']) !!}
        <a href="{{ route('pettyCashes.index') }}" class="btn text-maroon">@lang('crud.cancel')</a>
    </div>
</div>


@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    // trigger change on radio in case if validation error occured
    $(document).ready(function(){
        $('input:radio[name=trans_type]:checked').click();
    });

    $('#date').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    })
</script>
{{-- Add Vendor model --}}
@endsection