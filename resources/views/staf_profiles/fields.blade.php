@section('css')
@parent
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

<h3>General Information :</h3>
<br>
<div class="row">
<div class="form-group col-sm-6">
    <label for="exclude_from_expiry">Exclude from Expiry Alerts</label><br>

    <input type="checkbox"
           name="exclude_from_expiry"
           value="1"
           {{ isset($profile) && $profile->exclude_from_expiry ? 'checked' : '' }}>
</div>


    <div class="col-sm-4">
        <div class="form-group ">
            {!! Form::label('first_name', __('models/stafprofile.fields.name') .' *') !!}
            {!! Form::text('name', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group ">
            {!! Form::label('last_name', __('models/stafprofile.fields.last_name') .' *') !!}
            {!! Form::text('last_name', null, ['class' => $errors->has('last_name') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('last_name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('last_name') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <!-- Profile Picture Upload Field -->
        <div class="form-group">
            <div class="custom-file mt-4">
                {!! Form::file('profile_picture', ['class' => 'custom-file-input', 'id' => 'profile_picture']) !!}
                {!! Form::label('profile_picture', 'Profile Picture', ['class' => 'custom-file-label', 'id' => 'profile_picture_label']) !!}
            </div>
        </div>
    </div>    
    <div class="col-sm-4">
        <!-- Mobile Number Field -->
        <div class="form-group">
            {!! Form::label('mobile_no', 'Mobile No *') !!}
            {!! Form::tel('mobile_no', old('mobile_no', $profile->mobile_no ?? ''), [
                'class' => $errors->has('mobile_no') ? 'form-control is-invalid' : 'form-control',
                'aria-describedby' => 'mobile_no_help'
            ]) !!}
            <small id="mobile_no_help" class="form-text text-muted">Please enter your phone number including the country code (e.g. 971########).</small>
            @if ($errors->has('mobile_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('mobile_no') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">


        <!-- Home Mobile Number Field -->
        <div class="form-group">
            {!! Form::label('home_mobile_no', 'Home Mobile No') !!}
            {!! Form::tel('home_mobile_no', old('home_mobile_no', $profile->home_mobile_no ?? ''), [
                'class' => 'form-control',
                'aria-describedby' => 'home_mobile_no_help'
            ]) !!}
        </div>
    </div>    
    <div class="col-sm-4" id="staf_type_box">
        <div class="form-group">
            {!! Form::label('staf_type', __('models/stafprofile.fields.staf_type')) !!}
            <div class="input-group">
                {!! Form::select('staf_type', ['Office Staf' => 'Office Staf', 'Labor' => 'Labor','Managing Director' => 'Managing Director','Sponsor' => 'Sponsor','Driver' => 'Driver','Others' => 'Others'], null , ['class' => $errors->has('staf_type') ? 'form-control is-invalid' : 'form-control', 'id' => 'staf_type']) !!}
                @if ($errors->has('staf_type'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('staf_type') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-2" style="display:none" id="others_box">
        <div class="form-group ">
            {!! Form::label('staf_type','Please Mention'.' *') !!}
            {!! Form::text('', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control','id'=>'others_field']) !!}
            @if ($errors->has('staf_type'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('staf_type') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        @include(strtolower(__('models/stafprofile.plural')) . '.nationality')
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('gender',__('models/stafprofile.fields.gender')) !!}
            <div class="input-group">
                {!! Form::select('gender', ['Male' => 'Male', 'Female' => 'Female'],null, ['class' => $errors->has('gender') ? 'form-control is-invalid' : 'form-control', 'id' => 'gender']) !!}
                @if ($errors->has('gender'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('gender') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('joining_date',  __('models/stafprofile.fields.joining_date')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('joining_date', null, ['class' => $errors->has('joining_date') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile']) !!}
                @if ($errors->has('joining_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('joining_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('dob', __('models/stafprofile.fields.dob') .' *') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
                {!! Form::text('dob', null, ['class' => $errors->has('dob') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('dob'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('dob') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            {!! Form::label('basic_salary', __('models/stafprofile.fields.basic_salary')) !!}
            {!! Form::text('basic_salary', null, ['class' => $errors->has('basic_salary') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('basic_salary'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('basic_salary') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('total_salary', __('models/stafprofile.fields.total_salary')) !!}
            {!! Form::text('total_salary', null, ['class' => $errors->has('total_salary') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('total_salary'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('total_salary') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            {!! Form::label('overtime_rate', __('models/stafprofile.fields.overtime_rate')) !!}
            {!! Form::text('overtime_rate', null, ['class' => $errors->has('overtime_rate') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('overtime_rate'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('overtime_rate') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<h3>Expiry Dates :</h3>
<br>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('passport_expiry',  __('models/stafprofile.fields.passport_expiry')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('passport_expiry', null, ['class' => $errors->has('passport_expiry') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('passport_expiry'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('passport_expiry') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('visa_expiry',__('models/stafprofile.fields.visa_expiry'))!!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('visa_expiry', null, ['class' => $errors->has('visa_expiry') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('visa_expiry'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('visa_expiry') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('emirates_id_expiry',__('models/stafprofile.fields.emirates_id_expiry')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('emirates_id_expiry', null, ['class' => $errors->has('emirates_id_expiry') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('emirates_id_expiry'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('emirates_id_expiry') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('labor_card_expiry',__('models/stafprofile.fields.labor_card_expiry')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('labor_card_expiry', null, ['class' => $errors->has('labor_card_expiry') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('labor_card_expiry'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('labor_card_expiry') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4" style="display:none" id="driver_expiry_box">
        <div class="form-group">
            {!! Form::label('driver_permit_expiry',__('models/stafprofile.fields.driver_permit_expiry')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('driver_permit_expiry', null, ['class' => $errors->has('driver_permit_expiry') ? 'form-control is-invalid driver_expiry_field datePickerProfile' : 'form-control driver_expiry_field datePickerProfile',]) !!}
                @if ($errors->has('driver_permit_expiry'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('driver_permit_expiry') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@if (str_contains(url()->current(), '/edit'))
<h3>Last Info :</h3>
<br>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('last_increment',  __('models/stafprofile.fields.last_increment')) !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('last_increment', null, ['class' => $errors->has('last_increment') ? 'form-control is-invalid datePickerProfile' : 'form-control datePickerProfile',]) !!}
                @if ($errors->has('last_increment'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('last_increment') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group ">
            {!! Form::label('last_increment_amount', __('models/stafprofile.fields.last_increment_amount')) !!}
            {!! Form::number('last_increment_amount', null, ['class' => $errors->has('last_increment_amount') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('last_increment_amount'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('last_increment_amount') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
@endif

<br>
<h3>Documents Upload :</h3>
<a class="btn btn-success mb-2" id="add_new_field"><i class="fa fa-plus"></i> Add Document</a>

<div id="document-wrapper">
    <div class="file-row">
        @include(strtolower(__('models/stafprofile.plural')) . '.docs')
    </div>
</div>

<br>
<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('staf.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        var picker = $('.datePickerProfile').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        // Update the date fields and calculate difference on apply
        $('.datePickerProfile').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));

            // Fetch existing values from the fields if not set
            var startDate = $('#last_vacation_start').val() ? moment($('#last_vacation_start').val(), 'YYYY-MM-DD') : null;
            var endDate = $('#last_vacation_end').val() ? moment($('#last_vacation_end').val(), 'YYYY-MM-DD') : null;

            // Update the relevant date based on the field being updated
            if ($(this).attr('id') === 'last_vacation_start') {
                startDate = picker.startDate;
            } else if ($(this).attr('id') === 'last_vacation_end') {
                endDate = picker.startDate;
            }

            // Calculate and display difference if both dates are available
            if (startDate && endDate) {
                var differenceInDays = endDate.diff(startDate, 'days');
                $('#last_vacation_days').val(differenceInDays);
            } else {
                $('#last_vacation_days').val('');
            }
        });
        $('.datePickerProfile').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        var dayInput=$('.vic_day');
        dayInput.keyup(function(event){
            if(!(event.which != 8 && isNaN(String.fromCharCode(event.which)))){
                var days=dayInput.val();
                var vicEnd=$('.vic_start').data('daterangepicker').startDate.add(days,'days').format("YYYY-MM-DD");
                $('.vic_start').data('daterangepicker').startDate.subtract(days,'days').format("YYYY-MM-DD");
                $('.vic_end').data('daterangepicker').setStartDate(vicEnd);
                //  val(vicEnd);
            }
        });
        @if(isset($profile))
            var type=`{{$profile->staf_type}}`;
            if(type=='Driver'){
                $('#driver_expiry_box').show(300);
                $('.driver_expiry_field').val('{{$profile->driver_permit_expiry}}');
            }
            else if(type!='Office Staf' && type!='Labor' && type!='Managing Director' && type!='Sponsor'){
                $('#staf_type').val('Others');
                $('#staf_type').removeAttr("name");
                $('#others_field').val(type);
                $('#staf_type_box').removeClass('col-sm-4').addClass('col-sm-2');
                $('#others_box').show(300);
                $('#others_field').attr('name', 'staf_type');
                $('#others_field').prop('required',true);
            }
        @endif
        $('#staf_type').on('change', function(event) {
            if (event.target.value =='Others') {
                $('#staf_type_box').removeClass('col-sm-4').addClass('col-sm-2');
                $('#others_box').show(300);
                $('#others_field').attr('name', 'staf_type');
                $('#others_field').prop('required',true)
                $(this).removeAttr("name");
                $('#driver_expiry_box').hide(300);
                $('.driver_expiry_field').val(null);
                $('.driver_expiry_field').prop('required',false);
            }
            else if(event.target.value =='Driver'){
                $('#others_box').hide();
                $('#staf_type_box').removeClass('col-sm-2').addClass('col-sm-4');
                $('#others_field').val(null);
                $('#others_field').removeAttr("name");
                $('#others_field').prop('required',false);
                $(this).attr('name', 'staf_type');
                $('#driver_expiry_box').show(300);
                $('.driver_expiry_field').prop('required',true);
            }
            else{
                $('#others_box').hide();
                $('#staf_type_box').removeClass('col-sm-2').addClass('col-sm-4');
                $('#others_field').val(null);
                $('#others_field').removeAttr("name");
                $('#others_field').prop('required',false);
                $(this).attr('name', 'staf_type');
                $('#driver_expiry_box').hide(300);
                $('.driver_expiry_field').val(null);
                $('.driver_expiry_field').prop('required',false);
            }
        });
    </script>
@endsection
@section('scripts')
@parent
<script type="text/javascript">
    $(document).ready(function () {
        function bindEvents(row) {
            var fileName = row.find('.file-name');
            var fileLabel = row.find('.file-label');
            var fileType = row.find('.file-type');

            fileType.on('change', function () {
                var selected = $(this).val();
                fileName.attr('name', selected);  // Set the input name based on selection
                fileLabel.text(selected);  // Update the label based on selection
                fileLabel.attr('for', selected);
            });

            // Trigger the change once on load to set the correct initial name
            fileType.trigger('change');

            row.find('#del').click(function () {
                row.remove();
            });
        }

        var template = $('.file-row').first().clone();
        bindEvents($('.file-row').first()); // Bind to the original row

        $('#add_new_field').click(function () {
            var copy = template.clone();
            copy.find('.file-name').val('');
            copy.find('.file-label').text('Choose File');

            bindEvents(copy);
            $('#document-wrapper').append(copy);
        });
    });
</script>
@endsection


