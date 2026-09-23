<div class="row">
    <div class="col-md-3 col-sm-6">
        <!-- Ref No Field -->
        <div class="form-group">
            {!! Form::label('ref_no', __('models/lpoins.fields.ref_no').':') !!}
            {!! Form::text('ref_no', null, ['class' => ($errors->has('ref_no')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('ref_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('ref_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Quotation Id Field -->
        <div class="form-group">
            {!! Form::label('quotation_id', __('models/lpoins.fields.quotation_id').':') !!}
            {!! Form::select('quotation_id', $quotationItems, null, ['class' => ($errors->has('quotation_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'quotation_id']) !!}
            @if ($errors->has('quotation_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('quotation_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group" id="subject-group" style="display: none;">
        {!! Form::label('subject', 'Project Subject:') !!}
        {!! Form::text('subject', old('subject'), [
            'class' => $errors->has('subject') ? 'form-control is-invalid' : 'form-control',
            'id'    => 'subject'
        ]) !!}
        @if ($errors->has('subject'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('subject') }}</strong>
            </span>
        @endif
    </div>




    <div class="col-md-3 col-sm-6">
        <!-- Date Issue Field -->
        <div class="form-group">
            {!! Form::label('date_issue', __('models/lpoins.fields.date_issue').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date_issue', null, ['class' => ($errors->has('date_issue')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date_issue']) !!}
                @if ($errors->has('date_issue'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('date_issue') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- <div class="col-md-3 col-sm-6">
        <!-- Amount Field -->
        <div class="form-group">
            {!! Form::label('amount', __('models/lpoins.fields.amount').':') !!}
            {!! Form::text('amount', null, ['class' => ($errors->has('amount')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('amount'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('amount') }}</strong>
                </span>
            @endif
        </div>
    </div> --}}
    <div class="col-md-3 col-sm-6">
        <!-- File Field -->
        <div class="form-group">
            {!! Form::label('file','File:') !!}
            <div class="custom-file">
                {!! Form::file('files[]',['class' => 'custom-file-input', 'multiple' => true]) !!}
                {!! Form::label('file', __('models/lpoouts.fields.file').':' , ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Civil defence fee Field -->
        <div class="form-group">
            {!! Form::label('civil_defence_fee', __('models/lpoins.fields.civil_defence_fee').':') !!}
            {!! Form::text('civil_defence_fee', null, ['class' => ($errors->has('civil_defence_fee')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('civil_defence_fee'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('civil_defence_fee') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Goverment fee Field -->
        <div class="form-group">
            {!! Form::label('government_fee', __('models/lpoins.fields.government_fee').':') !!}
            {!! Form::text('government_fee', null, ['class' => ($errors->has('government_fee')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('government_fee'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('government_fee') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Adjustment fee Field -->
        <div class="form-group">
            {!! Form::label('adjustment_fee', __('models/lpoins.fields.adjustment_fee').':') !!}
            {!! Form::text('adjustment_fee', null, ['class' => ($errors->has('adjustment_fee')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('adjustment_fee'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('adjustment_fee') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <!-- Payment terms Field -->
        <div class="form-group">
            {!! Form::label('payment_terms', __('models/lpoins.fields.payment_terms').':') !!}
            {!! Form::text('payment_terms', null, ['class' => ($errors->has('payment_terms')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('payment_terms'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('payment_terms') }}</strong>
                </span>
            @endif
        </div>
    </div>

    @section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        bsCustomFileInput.init();

        $('#date_issue').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#quotation_id').select2({
            theme: 'bootstrap4',
            placeholder: "Select a Quotation",
            allowClear: true
        });

        // Listen to Select2 change event
        $('#quotation_id').on('change', function () {
            toggleSubjectField();
        });

        // Trigger once on page load in case a value is preselected
        toggleSubjectField();
    });
</script>

@if (!empty($isCreate) && $isCreate)
<script type="text/javascript">
    const quotationCategories = @json($quotationCategories);

    function toggleSubjectField() {
        const selectedId = $('#quotation_id').val();
        const category   = quotationCategories[selectedId];

        if (category === 'amc') {
            $('#subject-group').show();
        } else {
            $('#subject-group').hide();
            $('#subject').val('');
        }
    }

    $(document).ready(function () {
        toggleSubjectField();
        $('#quotation_id').on('change', toggleSubjectField);
    });
</script>
@endif

@endsection

    <div class="col-sm-12">
<!-- Submit Field -->
<div class="form-group">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg', 'id' => 'submit-btn']) !!}
    <a href="{{ route('lpoins.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('form').on('submit', function () {
            console.log("Form submitted at:", new Date().toISOString());
            $('#submit-btn').prop('disabled', true).addClass('disabled');
            $('#submit-btn').text('Processing...');
        });
    });
</script>
