<!-- Date Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date_time', __('models/petty_cashes.fields.date_time').':') !!}
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

<!-- voucher_no Field -->
<div class="form-group col-sm-6">
    {!! Form::label('voucher_no', __('models/petty_cashes.fields.voucher_no').':') !!}
    {!! Form::text('voucher_no', null, ['class' => ($errors->has('voucher_no')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('voucher_no'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('voucher_no') }}</strong>
        </span>
    @endif
</div>

<!-- Type Field -->
<div class="form-group">
    <label class="radio-inline">
        <input type="radio" name="trans_type" value="General" {{ old('trans_type') == '' || old('trans_type') == 'General' ? 'checked' : '' }} > General
    </label>
    <label class="radio-inline">
        <input type="radio" name="trans_type" value="Project" {{ old('trans_type') == 'Project' ? 'checked' : '' }}> Project
    </label>
    <label class="radio-inline">
        <input type="radio" name="trans_type" value="Advance" {{ old('trans_type') == 'Advance' ? 'checked' : '' }}> Give Advance
    </label>
</div>

<hr>
<div class="row">
    <div class="col-md-4">
        <!-- Deduct Field -->
        <div class="form-group">
            {!! Form::label('is_user_balance', 'Deduct From User Balance') !!}
            <label class="checkbox-inline">
                {!! Form::hidden('is_user_balance', 0) !!}
                {!! Form::checkbox('is_user_balance', '1', null) !!}
            </label>
        </div>
    </div>
    <div class="col-md-8">
        <!-- vendor Id Field -->
        <div class="form-group" id="div_vendor">
            {!! Form::label('vendor_id', __('models/petty_cashes.fields.vendor_id').':') !!}
            <div class="input-group">
                {!! Form::select('vendor_id', $vendorItems, null, ['class' => ($errors->has('vendor_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'vendor_id']) !!}
                <span class="input-group-append">
                    <button type="button" data-toggle="modal" data-target="#modal-vendor" class="btn btn-danger"><i class="fas fa-plus"></i></button>
                </span>
                @if ($errors->has('vendor_id'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('vendor_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
<hr>
<!-- Account Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('account_id', __('models/petty_cashes.fields.account_id').':') !!}
    {!! Form::select('account', $accountItems, null, ['class' => ($errors->has('account_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'account_id', 'readonly' => 'readonly']) !!}
    @if ($errors->has('account_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('account_id') }}</strong>
        </span>
    @endif
    <input type="hidden" name="account_id" value=""/>
</div>

<!-- Project Id Field -->
<div class="form-group col-sm-6" {{ ($errors->has('project_id')) || ( isset($pettyCash) && ($pettyCash->project_id != null) ) ? '' : 'hidden' }}  id="div_project">
    {!! Form::label('project_id', __('models/petty_cashes.fields.project_id').':') !!}
    {!! Form::select('project_id', $projectItems, null, ['class' => ($errors->has('project_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'project_id']) !!}
    @if ($errors->has('project_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('project_id') }}</strong>
        </span>
    @endif
</div>

<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', __('models/petty_cashes.fields.user_id').':') !!}
    {!! Form::select('user_id', $userItems, null, ['class' => ($errors->has('user_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'user_id']) !!}
    @if ($errors->has('user_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('user_id') }}</strong>
        </span>
    @endif
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', __('models/petty_cashes.fields.type').':') !!}
    {!! Form::select('type_id', $lookupItems, null, ['class' => ($errors->has('type')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'type']) !!}
    @if ($errors->has('type'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('type') }}</strong>
        </span>
    @endif
    <input type="hidden" name="type" value=""/>
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount', __('models/petty_cashes.fields.amount').':') !!}
    {!! Form::text('amount', null, ['class' => ($errors->has('amount')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('amount'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('amount') }}</strong>
        </span>
    @endif
</div>

<!-- Vat Field -->
<div class="form-group col-sm-6" id="div_vat">
    {!! Form::label('vat', __('models/petty_cashes.fields.vat').':') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('vat', 0) !!}
        {!! Form::checkbox('vat', '1', null) !!}
    </label>
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', __('models/petty_cashes.fields.description').':') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<!-- File Field -->
<div class="form-group col-sm-12">
    {!! Form::file('file',['class' => 'custom-file-input']) !!}
    {!! Form::label('file', __('models/petty_cashes.fields.file').':' , ['class' => 'custom-file-label']) !!}
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger']) !!}
    <a href="{{ route('pettyCashes.index') }}" class="btn text-maroon">@lang('crud.cancel')</a>
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

    $('#date_time').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss'
        }
    })

    $('#type').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option',
        allowClear: true
    })

    $('#vendor_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select Vendor',
        allowClear: true
    })

    $('#user_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option',
        allowClear: true
    })

    $('#project_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option',
        allowClear: true
    })

    $('#account_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option',
        allowClear: true
    });

    $('input[type=radio][name=trans_type]').on('click', function() {

        $("#account_id").val('')
        $("#account_id").prop("disabled", false).trigger('change');

        $("#type").val('')
        $("#type").prop("disabled", false).trigger('change');

        $("[name='is_user_balance']").prop("checked", false);
        $("[name='is_user_balance']").prop("disabled", false);

        if($(this).val() == 'Project'){
            $('#div_project').attr('hidden',false)
            $('#project_id').val('').trigger('change');

            $('#div_vendor').attr('hidden',false)
            $('#vendor_id').val('').trigger('change');


            $('#div_vat').attr('hidden',false)
            $('input[name=vat]').prop('checked', false);

        }else if($(this).val() == 'Advance'){
            // set account
            $("#account_id > option").each(function() {
                if(this.text == 'Advance'){
                    $("#account_id").val(this.value)
                    $("#account_id").prop("disabled", true).trigger('change');
                }
            });

            // set type
            $("#type > option").each(function() {
                if(this.text == 'Paid'){
                    $("#type").val(this.value)
                    $("#type").prop("disabled", true).trigger('change');
                }
            });

            //disable is user deduct
            $("[name='is_user_balance']").prop("checked", false);
            $("[name='is_user_balance']").prop("disabled", true);

            $('#div_project').attr('hidden',true)
            $('#div_vendor').attr('hidden',true)
            $('#div_vat').attr('hidden',true)
        }
        else{
            $('#div_project').attr('hidden',true)

            $('#div_vendor').attr('hidden',false)
            $('#vendor_id').val('').trigger('change');

            $('#div_vat').attr('hidden',false)
            $('input[name=vat]').prop('checked', false);
        }
    });

    $('#account_id').on('change', function() {
        $('[name="account_id"]').val($(this).val());
    });

    $('#type').on('change', function() {
        $('input[name="type"]').val($(this).val());
    });

    $('[name="is_user_balance"]').on('click', function() {
        if ($(this).prop("checked")){
            // set type
            $("#type > option").each(function() {
                if(this.text == 'Paid'){
                    $("#type").val(this.value)
                    $("#type").prop("disabled", true).trigger('change');
                }
            });
        }else{
            $("#type").val('')
            $("#type").prop("disabled", false).trigger('change');
        }

    });
    // Add Vendor
    function addVendor(e){
        e.preventDefault();

        var name = $("input[name=name]").val();
        var contact_no = $("input[name=contact_no]").val();
        var vat_no = $("input[name=vat_no]").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url: window.baseUrl(`/vendors/add/ajax`),
            data: {name:name,contact_no:contact_no,vat_no:vat_no},
            success:function(data){
                $("#vendor_id").append("<option value='"+data.id+"' selected>"+data.name+"</option>");
                $('#vendor_id').trigger('change');
                // select the latest added vendor
                var num = $('#vendor_id option').length;
                $('#vendor_id').prop('selectedIndex', num-1);

                clearForm()
                toast.fire({
                    type: 'success',
                    title: 'Vendor added Successfully.'
                });
            },
            error: function (xhr) {
                if (xhr.status == 422) {
                    var errors = JSON.parse(xhr.responseText);
                    $.each(errors, function(key, value) {
                        $("input[name="+key+"]").addClass('is-invalid');
                        $("input[name="+key+"]").next('span').text(value[0]);
                    });
                }
                if (xhr.status == 423) {
                    var errors = JSON.parse(xhr.responseText);
                    clearForm()
                    toast.fire({
                        type: 'error',
                        title: errors
                    });
                }
            }
        });
    }
    // clear model
    function clearForm(){
        $("input").removeClass('is-invalid');
        $(".invalid-feedback").text('');
        $('#vendor-form')[0].reset();
        $("#modal-vendor").modal('hide');
        document.getElementById('submit').removeAttribute('disabled');
    }
</script>
{{-- Add Vendor model --}}
<div class="modal fade" id="modal-vendor">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"> Add Vendor</h4>
        </div>
        <form id="vendor-form">
            <div class="modal-body">
                <div class="form-group">
                    <label>Vendor Name:</label>
                    <input type="text" name="name" class="form-control" />
                    <span class="invalid-feedback" >
                        <strong></strong>
                    </span>
                </div>
                <div class="form-group">
                    <label>Vendor Contact:</label>
                    <input type="text" name="contact_no" class="form-control"/>
                    <span class="invalid-feedback" >
                        <strong></strong>
                    </span>
                </div>
                <div class="form-group">
                    <label>Vendor Vat No:</label>
                    <input type="text" name="vat_no" class="form-control" />
                    <span class="invalid-feedback" >
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" onclick="clearForm()" class="btn btn-default">Close</button>
                <button type="button" id="submit" onclick="addVendor(event)" class="btn btn-danger">Add</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endsection
