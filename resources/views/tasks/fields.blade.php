@section('css')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
{{-- show all error --}}
@if ($errors->any())
    <div class="alert alert-danger">
            <strong>Whoops!</strong> These are the errors.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ ucfirst($error) }}</li>
                @endforeach
        </div>
    </div>
@endif
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('date',  __('models/tasks.fields.date').' *') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('start_date', null, ['class' => $errors->has('start_date') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile']) !!}
                @if ($errors->has('start_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('start_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group ">
            {!! Form::label('name', __('models/tasks.fields.name') .' *') !!}
            {!! Form::text('title', null, ['class' => $errors->has('title') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('title'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('title') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4" id="staf_type_box">
        <div class="form-group">
            {!! Form::label('assigned', __('models/tasks.fields.assigned').' *') !!}
            <div class="input-group">
                {!! Form::select('assigned', $users, null , ['class' => $errors->has('assigned') ? 'form-control is-invalid' : 'form-control', 'id' => 'assigned']) !!}
                @if ($errors->has('assigned'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('assigned') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('description', __('models/tasks.fields.description')) !!}
            {!! Form::text('description', null, ['class' => $errors->has('description') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('description'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('description') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('end_date',  __('models/tasks.fields.end_date')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('end_date', null, ['class' => $errors->has('end_date') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile']) !!}
                @if ($errors->has('end_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('end_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4" id="staf_type_box">
        <div class="form-group">
            {!! Form::label('status', __('models/tasks.fields.status')) !!}
            <div class="input-group">
                {!! Form::select('status', [1=>'Open',2=>'Close'], null , ['class' => $errors->has('status') ? 'form-control is-invalid' : 'form-control', 'id' => 'status']) !!}
                @if ($errors->has('status'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('task.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        var picker = $('.datePickerProfile').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: true,
            timePicker: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        });
        $('.datePickerProfile').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm'));
        });

        $('.datePickerProfile').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    </script>
@endsection
