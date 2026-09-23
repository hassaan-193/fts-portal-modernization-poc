<div class="row">
    <div class="col-md-3 col-sm-12">
        <div class="form-group">
            {!! Form::label('date', __('models/staff_payrolls.fields.date') . ':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'date']) !!}
            </div>
        </div>
    
        <div class="form-group">
            {!! Form::label('staf_type', __('models/stafprofile.fields.staf_type')) !!}
            {!! Form::select('staf_type', ['Office Staf' => 'Office Staf', 'Labor' => 'Labor', 'Managing Director' => 'Managing Director', 'Sponsor' => 'Sponsor', 'Driver' => 'Driver', 'Others' => 'Others'], null, ['class' => $errors->has('staf_type') ? 'form-control is-invalid' : 'form-control', 'id' => 'staf_type']) !!}
        </div>

        <div class="input-group-prepend">
            <button id="search_memebers" type="button" class="btn btn-danger btn-flat btn-lg">
                Search
            </button>
        </div>
    </div>
</div>
<hr>

@include('staff_payrolls.members')

<hr>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg', "onclick" => "return confirm('Are you sure?')"]) !!}
    <a href="{{ route('staffPayrolls.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>

@section('scripts')
@parent
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $('#date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM'
            }
        })
    </script>
@endsection
