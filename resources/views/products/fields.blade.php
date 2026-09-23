<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', __('models/products.fields.name').':') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>



<!-- Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('price', __('models/products.fields.price').':') !!}
    {!! Form::text('price', null, ['class' => ($errors->has('price')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('price'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('price') }}</strong>
        </span>
    @endif
</div>



<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger']) !!}
    <a href="{{ route('products.index') }}" class="btn text-maroon">@lang('crud.cancel')</a>
</div>
