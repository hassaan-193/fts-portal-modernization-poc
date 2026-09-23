<div class="row">
<!-- Date Field -->
<div class="col-md-3 col-sm-6">
<div class="form-group">
    {!! Form::label('date', __('models/projects.fields.date').':') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('date', null, ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date']) !!}
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
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
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

<!-- Quotation Id Field -->
<div class="col-md-3 col-sm-6">
    <div class="form-group">
        {!! Form::label('category', 'Project Category:') !!}
        <div>
            <label><input type="radio" name="category" value="amc" checked> AMC</label>
            <label><input type="radio" name="category" value="normal"> Normal</label>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-6">
    <div class="form-group">
        {!! Form::label('quotation_id', 'Select Quotation:') !!}
        <select name="quotation_id" id="quotation_id" class="form-control">
            <option value="">-- Select --</option>
            @foreach($amcQuotations as $id => $name)
                <option data-category="amc" value="{{ $id }}">{{ $name }}</option>
            @endforeach
            @foreach($normalQuotations as $id => $name)
                <option data-category="normal" value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryRadios = document.querySelectorAll('input[name="category"]');
        const quotationSelect = document.getElementById('quotation_id');
        const allOptions = Array.from(quotationSelect.options).filter(opt => opt.value !== "");

        function filterQuotations(category) {
            quotationSelect.innerHTML = '<option value="">-- Select --</option>';
            allOptions.forEach(option => {
                if (option.dataset.category === category) {
                    quotationSelect.appendChild(option);
                }
            });
        }

        categoryRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                filterQuotations(this.value);
            });
        });

        // Initial filter on page load
        filterQuotations(document.querySelector('input[name="category"]:checked').value);
    });
</script>
@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $('#quotation_id').select2({
            theme: 'bootstrap4',
            placeholder: "Select a Lpoin",
            allowClear: true
        })
    </script>
@endsection

<!-- Subject Field -->
<div  class="col-md-3 col-sm-6">
    <div class="form-group">
        {!! Form::label('subject', __('models/projects.fields.subject').':') !!}
        {!! Form::text('subject', null, ['class' => ($errors->has('subject')) ? 'form-control is-invalid' : 'form-control']) !!}
        @if ($errors->has('subject'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('subject') }}</strong>
            </span>
        @endif
    </div>
</div>

<!-- User Id Field -->
<div  class=" conditional-field form-group col-md-3 col-sm-6" style="display: none;" data-condition="Annual Maintenance Contract">
    {!! Form::label('user_id', __('models/projects.fields.user_id').':') !!}
    {!! Form::select('user_id', $users, null, ['class' => ($errors->has('user_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'user_id']) !!}
    @if ($errors->has('user_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('user_id') }}</strong>
        </span>
    @endif
</div>

<!-- Labour Charges Field -->
<div class="conditional-field col-md-3 col-sm-6" style="display: none;" data-condition="Annual Maintenance Contract">
<div class="form-group">
    {!! Form::label('labour_charges', __('models/projects.fields.labour_charges').':') !!}
    {!! Form::text('labour_charges', null, ['class' => ($errors->has('labour_charges')) ? 'form-control is-invalid' : 'form-control', 'id' => 'labour_charges']) !!}
    @if ($errors->has('labour_charges'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('labour_charges') }}</strong>
        </span>
    @endif
</div>
    </div>
<!-- Material Charges Field -->
<div class="conditional-field col-md-3 col-sm-6" style="display: none;" data-condition="Annual Maintenance Contract">
<div class="form-group">
    {!! Form::label('material_charges', __('models/projects.fields.material_charges').':') !!}
    {!! Form::text('material_charges', null, ['class' => ($errors->has('material_charges')) ? 'form-control is-invalid' : 'form-control', 'id' => 'material_charges']) !!}
    @if ($errors->has('material_charges'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('material_charges') }}</strong>
        </span>
    @endif
</div>
    </div>
<!-- Project Source Field -->
<div  class="conditional-field col-md-3 col-sm-6" style="display: none;" data-condition="Annual Maintenance Contract">
<div class="form-group">
    {!! Form::label('project_source', __('models/projects.fields.project_source').':') !!}
    {!! Form::text('project_source', null, ['class' => ($errors->has('project_source')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('project_source'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('project_source') }}</strong>
        </span>
    @endif
</div>
    </div>
<!-- Project estimation Field -->
<div class="conditional-field col-md-3 col-sm-6" style="display: none;" data-condition="Annual Maintenance Contract">
<div class="form-group">
    {!! Form::label('project_estimation', __('models/projects.fields.project_estimation').':') !!}
    {!! Form::text('project_estimation', null, ['class' => ($errors->has('project_estimation')) ? 'form-control is-invalid' : 'form-control', 'id' => 'project_estimation', 'readonly' => true]) !!}
    @if ($errors->has('project_estimation'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('project_estimation') }}</strong>
        </span>
    @endif
</div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labourInput = document.getElementById('labour_charges');
        const materialInput = document.getElementById('material_charges');
        const estimationInput = document.getElementById('project_estimation');

        function updateProjectEstimation() {
            const labour = parseFloat(labourInput.value) || 0;
            const material = parseFloat(materialInput.value) || 0;
            estimationInput.value = (labour + material).toFixed(2);
        }

        labourInput.addEventListener('input', updateProjectEstimation);
        materialInput.addEventListener('input', updateProjectEstimation);
    });
</script>
     
<!-- Visit Fields -->
<div class="col-md-3 col-sm-6">
    <div class="form-group">
        {!! Form::select('category', ['normal' => 'Normal', 'amc' => 'AMC'], null, ['class' => 'form-control', 'id' => 'project_category','hidden' => true]) !!}
    </div>
    <div class="form-group" id="visit_field" style="display: none;">
        {!! Form::number('visits', 4, ['class' => 'form-control', 'min' => 4,'max'=>12,'hidden' => true]) !!}
    </div>
</div>

<!-- Note Field -->
<div class="col-md-12">
<div class="form-group">
    {!! Form::label('note', __('models/projects.fields.note').':') !!}
    {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
</div>
    </div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('projects.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="category"]');
        const conditionalFields = document.querySelectorAll('.conditional-field');
        const toggleFieldsVisibility = () => {
            const selectedValue = document.querySelector('input[name="category"]:checked').value;
            conditionalFields.forEach(field => {
                if (selectedValue === 'amc') {
                    field.style.display = 'none';
                } else if (selectedValue === 'normal') {
                    field.style.display = 'block';
                }
            });
        };
        radios.forEach(radio => {
            radio.addEventListener('change', toggleFieldsVisibility);
        });
        toggleFieldsVisibility();
    });
</script>
