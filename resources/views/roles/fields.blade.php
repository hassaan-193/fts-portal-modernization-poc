<div class="form-group">
    {!! Form::label('name', 'Name *') !!}
    {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control'] ) !!}
    @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <label>Permissions</label>
    <div class="row">
        @foreach($permissions as $permission)
            <div class="col-sm-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                        {{(isset($role) && $role->permissions->contains('name', $permission->name)) ? 'checked' : ''}}
                        {{(isset($role) && $role->id == 1 ) ? 'disabled' : ''}}
                        name="permissions[]"  value="{{ $permission->id }}" id="{{$permission->id}}" >
                    <label class="custom-control-label" for="{{$permission->id}}">{{ $permission->name}} </label>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('roles.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
</div>
