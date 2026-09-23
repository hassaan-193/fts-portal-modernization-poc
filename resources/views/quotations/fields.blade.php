<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('name', __('models/quotations.fields.name') . ':') !!}
            {!! Form::text('name', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <!-- Quotaion type Field -->
        <div class="form-group">
            {!! Form::label('quotation_type_id', __('models/quotations.fields.quotation_type_id') . ':') !!}
            <div class="input-group">
                {!! Form::select('quotation_type_id', $typeItems, null, [
                    'class' => $errors->has('quotation_type_id') ? 'form-control is-invalid' : 'form-control',
                    'id' => 'quotation_type_id',
                ]) !!}
                <span class="input-group-append">
                    <button type="button" data-toggle="modal" data-target="#modal-type" class="btn btn-danger"><i
                            class="fas fa-plus"></i></button>
                </span>
                @if ($errors->has('quotation_type_id'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('quotation_type_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>

    </div>

    @section('css')
        @parent
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    @endsection
    <div class="col-md-4 col-sm-6">
        <!-- Company Id Field -->
        <div class="form-group">
            {!! Form::label('company_id', __('models/quotations.fields.company_id') . ':') !!}
            <div class="input-group">
                {!! Form::select('company_id', $companyItems, null, [
                    'class' => $errors->has('company_id') ? 'form-control is-invalid' : 'form-control',
                    'id' => 'company_id',
                ]) !!}
                <span class="input-group-append">
                    <button type="button" data-toggle="modal" data-target="#modal-company" class="btn btn-danger"><i
                            class="fas fa-plus"></i></button>
                </span>
                @if ($errors->has('company_id'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('company_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    @section('scripts')
        @parent
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <script>
            $('#company_id').select2({
                theme: 'bootstrap4'
            });
            $('#reference_project_id').select2({
                theme: 'bootstrap4',
                placeholder: '— Select Project —',
                allowClear: true
            });
        </script>
    @endsection

    <!-- Quotaion company Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('quotation_company', __('models/quotations.fields.quotation_company') . ':') !!}
            <div class="input-group">
                {!! Form::select('quotation_company', $typeCompany, null, [
                    'class' => $errors->has('quotation_company') ? 'form-control is-invalid' : 'form-control',
                ]) !!}
                @if ($errors->has('quotation_company'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('quotation_company') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Ref No Field -->
        <div class="form-group">
            {!! Form::label('ref_no', __('models/quotations.fields.ref_no') . ':') !!}
            {!! Form::text('ref_no', null, ['class' => $errors->has('ref_no') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('ref_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('ref_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- Amount Field -->
        <div class="form-group">
            {!! Form::label('amount', __('models/quotations.fields.amount') . ':') !!}
            {!! Form::text('amount', null, ['class' => $errors->has('amount') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('amount'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('amount') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Date Field -->
        <div class="form-group">
            {!! Form::label('date', __('models/quotations.fields.date') . ':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, [
                    'class' => $errors->has('date') ? 'form-control is-invalid' : 'form-control',
                    'id' => 'date',
                ]) !!}
                @if ($errors->has('date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    @section('scripts')
        @parent
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script type="text/javascript">
            $('#date').daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            })
        </script>
    @endsection
    <div class="col-md-3 col-sm-6">

        <!-- Subject Field -->
        <div class="form-group">
            {!! Form::label('subject', __('models/quotations.fields.subject') . ':') !!}
            {!! Form::text('subject', null, [
                'class' => $errors->has('subject') ? 'form-control is-invalid' : 'form-control',
            ]) !!}
            @if ($errors->has('subject'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('subject') }}</strong>
                </span>
            @endif
        </div>

    </div>
    <div class="col-md-3 col-sm-6">

        <!-- Location Field -->
        <div class="form-group">
            {!! Form::label('location', __('models/quotations.fields.location') . ':') !!}
            {!! Form::text('location', null, ['class' => 'form-control']) !!}
        </div>
    </div>

<!-- Link to Existing Project Toggle - Only for AMC quotations -->
<div class="col-md-4 col-sm-6" id="link_project_wrapper" style="display: none;">
    <div class="form-group">
        <label>Link to Existing AMC Project:</label>
        <div class="custom-control custom-switch">
            {!! Form::checkbox('link_to_existing_project', 1, false, [
                'class' => 'custom-control-input',
                'id' => 'link_to_existing_project'
            ]) !!}
            <label class="custom-control-label" for="link_to_existing_project">
                Enable linking to existing project
            </label>
        </div>
    </div>
</div>

<!-- Category Field -->
<div class="col-md-4 col-sm-6" id="category-field" style="display: none;">
    <div class="form-group">
        {!! Form::label('category', __('category') . ':') !!}
        {!! Form::text('category', 'amc', [
            'class' => $errors->has('category') ? 'form-control is-invalid' : 'form-control',
            'readonly' => 'readonly'
        ]) !!}
        @if ($errors->has('category'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('category') }}</strong>
            </span>
        @endif
    </div>
</div>

<!-- Project Reference Field - For linking to existing AMC projects (uses company_id from main form) -->
<div class="col-md-4 col-sm-6" id="project_reference_section" style="display: none;">
    <div class="form-group">
        {!! Form::label('reference_project_id', 'Select Existing AMC Project:') !!}
        {!! Form::select('reference_project_id', [], isset($quotation) && $quotation->reference_project_id ? [$quotation->reference_project_id => $quotation->referenceProject->subject] : [], [
            'class' => $errors->has('reference_project_id') ? 'form-control is-invalid' : 'form-control',
            'id' => 'reference_project_id',
            'placeholder' => '— Select Project —'
        ]) !!}
        @if ($errors->has('reference_project_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('reference_project_id') }}</strong>
            </span>
        @endif
    </div>
</div>


<!-- Number of Visits Field -->
<div class="col-md-4 col-sm-6" id="number_of_visits-field" style="display: none;">
    <div class="form-group">
        {!! Form::label('number_of_visits', __('visits') . ':') !!}
        {!! Form::number('number_of_visits', 4, [
            'class' => $errors->has('number_of_visits') ? 'form-control is-invalid' : 'form-control',
            'min' => 4,
            'max' => 12
        ]) !!}
        @if ($errors->has('number_of_visits'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('number_of_visits') }}</strong>
            </span>
        @endif
    </div>
</div>




    <div class="col-md-3 col-sm-6">
        <!-- File Field -->
        <div class="form-group">
            <div class="custom-file mt-3">
                {!! Form::file('file', ['class' => 'custom-file-input']) !!}
                {!! Form::label('file', __('models/quotations.fields.file') . ':', ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row" id="payment_terms">
    <!-- payment Field -->
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            {!! Form::label('payment', __('models/quotations.fields.payment').':') !!}
            {!! Form::textarea('payment',
            '<ul>
                <li>100% Advance payment (Non- Refundable) -TRN Number: (100317831400003)</li>
                <li>Payment/Cheque should be issued on or before 7 working days of each invoice generated.</li>
                <li># Validity : 7 days</li>
                <li># Delivery date: It will be after the advanced payment, subjected to the stock availability.</li>
            </ul>
            '
            , ['class' => 'form-control summer-note']) !!}
        </div>
    </div>
    @section('scripts')
        @parent
        <script src=" {{ asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
        <script>
            $(function () {
                    $('.summer-note').summernote({
                        height: 250
                    })
                })
        </script>
    @endsection

    <!-- exclusion Field -->
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            {!! Form::label('exclusion', __('models/quotations.fields.exclusion').':') !!}
            {!! Form::textarea('exclusion',
            '<ul>
                <li># Government fees, any new requirements from Civil Defense or regulations, Land Lord, Client, Consultant, Fire rated doors, gate pass, Voice evacuation, kitchen hood, water tank. LPG Electricity, Civil works & Manhole works.</li>
                <li>#Any new requirements not mentioned in the above BOQ.</li>
                <li>#This is not the final quote as some further maintenance might be required after trouble shooting checks.</li>
                <li># Any third-party charges.</li>
            </ul>
            <p>We hope our offer is in line with your requirements and we looking forward to hear from you soon. For any details, kindly contact undersigned</p>
            <p>Thanks, and Regards,</p>
            ',
            ['class' => 'form-control summer-note']) !!}
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection

<!-- Services Field -->
@include('quotations.add_products',[
    'quotation' => isset($quotation) ? $quotation : null
])

<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('quotations.index') }}"
                class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script>
        // Add Company
        function addCompany(e) {
            e.preventDefault();

            var company_name = $("input[name=company_name]").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: window.baseUrl(`/companies/add/ajax`),
                data: {
                    company_name: company_name
                },
                success: function(data) {
                    $("#company_id").append("<option value='" + data.id + "' selected>" + data.name +
                        "</option>");
                    $('#company_id').trigger('change');
                    // select the latest added company
                    var num = $('#company_id option').length;
                    $('#company_id').prop('selectedIndex', num - 1);

                    clearForm()
                    toast.fire({
                        type: 'success',
                        title: 'Company added Successfully.'
                    });
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText);
                        $.each(errors, function(key, value) {
                            $("input[name=" + key + "]").addClass('is-invalid');
                            $("input[name=" + key + "]").next('span').text(value[0]);
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
        // Add Type
        function addType(e) {
            e.preventDefault();

            var type_name = $("input[name=type_name]").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: window.baseUrl(`/quotations/add/type/ajax`),
                data: {
                    type_name: type_name
                },
                success: function(data) {
                    $("#quotation_type_id").append("<option value='" + data.id + "' selected>" + data.name +
                        "</option>");
                    $('#quotation_type_id').trigger('change');
                    // select the latest added type
                    var num = $('#quotation_type_id option').length;
                    $('#quotation_type_id').prop('selectedIndex', num - 1);

                    clearFormType()
                    toast.fire({
                        type: 'success',
                        title: 'Type added Successfully.'
                    });
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText);
                        $.each(errors, function(key, value) {
                            $("input[name=" + key + "]").addClass('is-invalid');
                            $("input[name=" + key + "]").next('span').text(value[0]);
                        });
                    }
                    if (xhr.status == 423) {
                        var errors = JSON.parse(xhr.responseText);
                        clearFormType()
                        toast.fire({
                            type: 'error',
                            title: errors
                        });
                    }
                }
            });
        }
        // clear model
        function clearForm() {
            $("input").removeClass('is-invalid');
            $(".invalid-feedback").text('');
            $('#company-form')[0].reset();
            $("#modal-company").modal('hide');
            document.getElementById('submit').removeAttribute('disabled');
        }

        function clearFormType() {
            $("input").removeClass('is-invalid');
            $(".invalid-feedback").text('');
            $('#type-form')[0].reset();
            $("#modal-type").modal('hide');
            document.getElementById('submit').removeAttribute('disabled');
        }

        $('#quotation_type_id').change(function() {
            var selectedText = $('#quotation_type_id option:selected').text();
            if (selectedText === 'Annual Maintenance Contract' || selectedText === 'Maintenance') {
                $('#quotation_product_div').show();
                $('#payment_terms').show();
            } else {
                $('#quotation_product_div').hide();
                $('#payment_terms').hide();
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Check the initial selection of the dropdown
            toggleCategoryFields();

            // When the quotation type changes, toggle the fields
            $('#quotation_type_id').change(function() {
                toggleCategoryFields();
            });

            // When main company_id changes, load projects if toggle is enabled
            $('#company_id').change(function() {
                if ($('#link_to_existing_project').is(':checked')) {
                    var companyId = $(this).val();
                    if (companyId) {
                        loadProjectsForCompany(companyId);
                    } else {
                        $('#reference_project_id').html('<option value="">— Select Project —</option>');
                    }
                }
            });

            // Toggle project linking section
            $('#link_to_existing_project').change(function() {
                if ($(this).is(':checked')) {
                    $('#project_reference_section').show();
                    $('#number_of_visits-field').hide();
                    // Load projects for the currently selected company
                    var companyId = $('#company_id').val();
                    if (companyId) {
                        loadProjectsForCompany(companyId);
                    } else {
                        alert('Please select a company first');
                        $(this).prop('checked', false);
                        $('#project_reference_section').hide();
                        $('#number_of_visits-field').show();
                    }
                } else {
                    $('#project_reference_section').hide();
                    $('#number_of_visits-field').show();
                    $('#reference_project_id').val('');
                    $('#reference_project_id').html('<option value="">— Select Project —</option>');
                }
            });

            function toggleCategoryFields() {
                var selectedType = $('#quotation_type_id').val();
                var amcTypes = [6, 7, 16, 22];

                // Always show the linking toggle for any quotation type
                $('#link_project_wrapper').show();

                // Show AMC-specific fields only for AMC types
                if (amcTypes.includes(parseInt(selectedType))) {
                    $('#category-field').show();
                    $('#number_of_visits-field').show();
                    $('#category').val('amc');
                } else {
                    $('#category-field').hide();
                    $('#number_of_visits-field').hide();
                    $('#category').val('');
                }
            }

            function loadProjectsForCompany(companyId) {
                $.ajax({
                    url: '{{ route("get-amc-projects") }}',
                    type: 'GET',
                    data: { company_id: companyId },
                    success: function(data) {
                        var options = '<option value="">— Select Project —</option>';
                        if (Object.keys(data).length === 0) {
                            options = '<option value="">No AMC projects found for this company</option>';
                        } else {
                            $.each(data, function(id, name) {
                                options += '<option value="' + id + '">' + name + '</option>';
                            });
                        }
                        $('#reference_project_id').html(options);
                        // Reinitialize Select2 after loading new options
                        $('#reference_project_id').select2({
                            theme: 'bootstrap4',
                            placeholder: '— Select Project —',
                            allowClear: true
                        });
                    },
                    error: function() {
                        $('#reference_project_id').html('<option value="">Error loading projects</option>');
                        $('#reference_project_id').select2({
                            theme: 'bootstrap4',
                            placeholder: '— Select Project —',
                            allowClear: true
                        });
                    }
                });
            }
        });
    </script>

    {{-- Add company model --}}
    <div class="modal fade" id="modal-company">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Add company</h4>
                </div>
                <form id="company-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Company Name:</label>
                            <input type="text" name="company_name" class="form-control" />
                            <span class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" onclick="clearForm()" class="btn btn-default">Close</button>
                        <button type="button" id="submit" onclick="addCompany(event)"
                            class="btn btn-danger">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Quotation Type model --}}
    <div class="modal fade" id="modal-type">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Add Quotation Type</h4>
                </div>
                <form id="type-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Type Name:</label>
                            <input type="text" name="type_name" class="form-control" />
                            <span class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" onclick="clearFormType()"
                            class="btn btn-outline-danger btn-flat btn-lg text-maroon">Close</button>
                        <button type="button" id="submit" onclick="addType(event)"
                            class="btn btn-danger btn-flat btn-lg">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
