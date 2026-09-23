<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', __('models/request_forms.fields.name').':') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>



@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
@endsection

<!-- Date Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date_time', __('models/request_forms.fields.date_time').':') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('date_time', null, ['class' => ($errors->has('date_time')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date_time']) !!}
        @if ($errors->has('date_time'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('date_time') }}</strong>
            </span>
        @endif
    </div>
</div>

@section('scripts')
@parent
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript">
        $('#date_time').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            }
        })
    </script>
@endsection

<!-- Note Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('note', __('models/request_forms.fields.note').':') !!}
    {!! Form::textarea('note', null, ['class' => ($errors->has('note')) ? 'form-control is-invalid' : 'form-control'] ) !!}
    @if ($errors->has('note'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('note') }}</strong>
            </span>
        @endif
</div>

    <!-- File Field -->
    <div class="form-group col-sm-12 col-lg-12">
        <div class="custom-file mt-3">
            {!! Form::file('file',['class' => 'custom-file-input']) !!}
            {!! Form::label('file', __('models/request_forms.fields.file').':' , ['class' => 'custom-file-label']) !!}
        </div>
    </div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('requestForms.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
