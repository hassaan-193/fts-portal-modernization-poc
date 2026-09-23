<div class="row">
    <div class="col-md-3 col-sm-6">
        <!-- LPOIN ID Field -->
        <div class="form-group">
            {!! Form::label('lpoin_id', __('models/drawing_receiveds.fields.lpoin_id').':') !!}
            {!! Form::select('lpoin_id', $lpoins, null, ['class' => ($errors->has('lpoin_id')) ? 'form-control is-invalid' : 'form-control', 'id' => 'lpoin_id', 'placeholder' => 'Select LPOIN']) !!}
            @if ($errors->has('lpoin_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('lpoin_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Responsible Engineer ID Field -->
        <div class="form-group">
            {!! Form::label('responsible_engineer_id', __('models/drawing_receiveds.fields.responsible_engineer_id').':') !!}
            {!! Form::select('responsible_engineer_id', $engineers, null, ['class' => ($errors->has('responsible_engineer_id')) ? 'form-control is-invalid' : 'form-control', 'id' => 'responsible_engineer_id', 'placeholder' => 'Select Engineer']) !!}
            @if ($errors->has('responsible_engineer_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('responsible_engineer_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Type of Work Field -->
        <div class="form-group">
            {!! Form::label('type_of_work', __('models/drawing_receiveds.fields.type_of_work').':') !!}
            {!! Form::select('type_of_work', $typeOfWork, null, ['class' => ($errors->has('type_of_work')) ? 'form-control is-invalid' : 'form-control', 'id' => 'type_of_work', 'placeholder' => 'Select Type']) !!}
            @if ($errors->has('type_of_work'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('type_of_work') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Status Field -->
        <div class="form-group">
            {!! Form::label('status', __('models/drawing_receiveds.fields.status').':') !!}
            {!! Form::select('status', $statusOptions, null, ['class' => ($errors->has('status')) ? 'form-control is-invalid' : 'form-control', 'id' => 'status', 'placeholder' => 'Select Status']) !!}
            @if ($errors->has('status'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('status') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Start Date Field -->
        <div class="form-group">
            {!! Form::label('start_date', __('models/drawing_receiveds.fields.start_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('start_date', null, ['class' => ($errors->has('start_date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'start_date']) !!}
                @if ($errors->has('start_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('start_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Review Comments Date Field -->
        <div class="form-group">
            {!! Form::label('review_comments_date', __('models/drawing_receiveds.fields.review_comments_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('review_comments_date', null, ['class' => ($errors->has('review_comments_date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'review_comments_date']) !!}
                @if ($errors->has('review_comments_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('review_comments_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Approval Date Field -->
        <div class="form-group">
            {!! Form::label('approval_date', __('models/drawing_receiveds.fields.approval_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('approval_date', null, ['class' => ($errors->has('approval_date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'approval_date']) !!}
                @if ($errors->has('approval_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('approval_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Attachments Field -->
        <div class="form-group">
            {!! Form::label('attachments', 'Attachments:') !!}
            <div class="custom-file">
                {!! Form::file('attachments[]', ['class' => 'custom-file-input', 'multiple' => true, 'id' => 'attachments']) !!}
                {!! Form::label('attachments', 'Choose Files', ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-12 col-sm-12">
        <!-- Notes Field -->
        <div class="form-group">
            {!! Form::label('notes', __('models/drawing_receiveds.fields.notes').':') !!}
            {!! Form::textarea('notes', null, ['class' => ($errors->has('notes')) ? 'form-control is-invalid' : 'form-control', 'rows' => 4]) !!}
            @if ($errors->has('notes'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('notes') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-12 col-sm-12">
        <!-- Submit and Cancel Buttons -->
        <div class="form-group">
            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-primary']) !!}
            <a href="{{ route('drawing-receiveds.index') }}" class="btn btn-secondary">@lang('crud.cancel')</a>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        bsCustomFileInput.init();

        // Initialize date pickers
        $('#start_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#review_comments_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#approval_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        // Initialize select2
        $('#lpoin_id, #responsible_engineer_id, #type_of_work, #status').select2();
    });
</script>
@endsection
