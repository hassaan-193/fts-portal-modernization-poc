<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', __('models/employees.fields.name').':') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>



<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', __('models/employees.fields.email').':') !!}
    {!! Form::text('email', null, ['class' => ($errors->has('email')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('email'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
</div>



<!-- Contact No Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contact_no', __('models/employees.fields.contact_no').':') !!}
    {!! Form::text('contact_no', null, ['class' => ($errors->has('contact_no')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('contact_no'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('contact_no') }}</strong>
        </span>
    @endif
</div>



<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('employees.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>
