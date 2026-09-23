<div class="row">
    <div class="col-sm-6">
        <div class="form-group ">
            {!! Form::label('name', 'Name *') !!}
            {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control'] ) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group ">
            {!! Form::label('email', 'Email *') !!}
            {!! Form::text('email', null, ['class' => ($errors->has('email')) ? 'form-control is-invalid' : 'form-control'] ) !!}
            @if ($errors->has('email'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group ">
            {!! Form::label('password', 'Password') !!}
            <input type="password" class="form-control {{ $errors->has('password')?'is-invalid':'' }}" name="password" value="{{ old('password') }}" >
            @if ($errors->has('password'))
                <span class="invalid-feedback">
                <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group ">
            {!! Form::label('password_confirmation', 'Confirm Password') !!}
            <input type="password" name="password_confirmation" class="form-control" >
            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <!-- File Field -->
        <div class="form-group">
            {!! Form::label('signature', 'Signature') !!}
            <div class="custom-file">
                {!! Form::file('file',['class' => 'custom-file-input']) !!}
                {!! Form::label('file', __('models/companies.fields.file').':' , ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group ">
            {!! Form::label('staf_profile_id', 'Linked Staff Profile') !!}
            {!! Form::select('staf_profile_id', $stafProfiles->pluck('name', 'id')->prepend('-- None --', ''), null, ['class' => $errors->has('staf_profile_id') ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('staf_profile_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('staf_profile_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group ">
            <label>Role</label>
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-sm-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input {{ $errors->has('role')?'is-invalid':'' }}"
                            id="{{$role->id}}" name="role[]"
                            {{( isset($user) && $user->hasRole($role->name) ) ? 'checked' : ''}}
                            onclick="return {{ ( isset($user) && $user->id == 1 && $role->id == 1 ) ? 'false' : 'true' }} ;"
                            value="{{ $role->id }}">
                            <label class="custom-control-label" for="{{$role->id}}">{{ $role->name }} </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit('Save', ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">Cancel</a>
        </div>
    </div>
</div>
