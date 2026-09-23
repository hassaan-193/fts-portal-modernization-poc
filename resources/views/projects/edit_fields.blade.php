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
        {!! Form::text('date', null, ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date','disabled'=>true]) !!}
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
<div class="col-md-6 col-sm-6">
    <div class="form-group">
        {!! Form::label('quotation_id', __('models/lpoins.singular').':') !!}
        <input type="hidden" name="quotation_id" value=" {{ $project->quotation_id}}" />
        {!! Form::text('lpoin', $lpoin , ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control' , 'readonly' => 'readonly' ]) !!}
        @if ($errors->has('quotation_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('quotation_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<!-- Subject Field -->
<div class="col-md-3 col-sm-6">
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
<div class="form-group col-md-3 col-sm-6">
    {!! Form::label('user_id', __('models/projects.fields.user_id').':') !!}
    {!! Form::select('user_id', $users, null, ['class' => ($errors->has('user_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'user_id']) !!}
    @if ($errors->has('user_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('user_id') }}</strong>
        </span>
    @endif
</div>

<!-- Labour Charges Field -->
<div class="col-md-3 col-sm-6">
<div class="form-group">
    {!! Form::label('labour_charges', __('models/projects.fields.labour_charges').':') !!}
    {!! Form::text('labour_charges', null, ['class' => ($errors->has('labour_charges')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('labour_charges'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('labour_charges') }}</strong>
        </span>
    @endif
</div>
    </div>
<!-- Material Charges Field -->
<div class="col-md-3 col-sm-6">
<div class="form-group">
    {!! Form::label('material_charges', __('models/projects.fields.material_charges').':') !!}
    {!! Form::text('material_charges', null, ['class' => ($errors->has('material_charges')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('material_charges'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('material_charges') }}</strong>
        </span>
    @endif
</div>
    </div>
<!-- Project Source Field -->
<div class="col-md-3 col-sm-6">
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
<div class="col-md-3 col-sm-6">
<div class="form-group">
    {!! Form::label('project_estimation', __('models/projects.fields.project_estimation').':') !!}
    {!! Form::text('project_estimation', null, ['class' => ($errors->has('project_estimation')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('project_estimation'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('project_estimation') }}</strong>
        </span>
    @endif
</div>
    </div>

<!-- Visit Fields -->
<div class="col-md-3 col-sm-6">
    <div class="form-group">
        {!! Form::label('category', 'Project Category') !!}
        {!! Form::select('category', ['normal' => 'Normal', 'amc' => 'AMC'], old('category', $project->category), ['class' => 'form-control', 'id' => 'project_category','disabled'=>true]) !!}
        {!! Form::hidden('category', $project->category) !!}
    </div>
    <div class="form-group" id="visit_field" style="display: {{ $project->category === 'amc' ? 'block' : 'none' }};">
        {!! Form::label('visits', 'Number of Visits') !!}
        {!! Form::number('visits', old('visits', $project->visits ?? 4), ['class' => 'form-control', 'min' => 4, 'max' => 12,'readonly'=>true]) !!}
        {!! Form::hidden('visits', $project->visits ?? 4) !!}
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
