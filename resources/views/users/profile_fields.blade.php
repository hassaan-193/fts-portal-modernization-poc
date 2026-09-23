<div class="text-center">
    <img class="profile-user-img img-fluid img-circle"
    src="{{ isset($user->image) ? asset('dist/img/'.$user->image) : asset('dist/img/avatar.png') }}"
    {{-- src="{{ isset($user->image) ? .$driver->user_profile->path : asset('dist/img/avatar.png') }}"  --}}
    alt="User profile picture">
</div>
<h3 class="profile-username text-center">{{ $user->name }}</h3>
<p class="text-muted text-center">
    @foreach($user->roles as $role)
       {{ $role->name .' '}}
    @endforeach
</p>
<ul class="list-group list-group-unbordered mb-3">
    <li class="list-group-item">
    <b>EMAIL</b> <a class="float-right">{{ $user->email}}</a>
    </li>
</ul>
<div class="form-group ">
    {!! Form::label('password', 'Password') !!}
    <input type="password" class="form-control {{ $errors->has('password')?'is-invalid':'' }}" name="password" value="{{ old('password') }}" >
    @if ($errors->has('password'))
        <span class="invalid-feedback">
        <strong>{{ $errors->first('password') }}</strong>
        </span>
    @else
        <span class="help-block text-danger" style="font-size: small;">Please leave it blank if you don't want to change password</span>
    @endif
</div>
<div class="form-group ">
    {!! Form::label('password_confirmation', 'Confirm Password') !!}
    <input type="password" name="password_confirmation" class="form-control" >
    @if ($errors->has('password_confirmation'))
        <span class="help-block">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
        </span>
    @endif
</div>
<div class="form-group">
    <label for="image">Profile Image</label>
    <div class="input-group">
      <div class="custom-file">
        <input type="file" class="custom-file-input" name="image" id="image">
        <label class="custom-file-label" for="image">Choose file</label>
      </div>
    </div>
</div>
<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Update', ['class' => 'btn btn-danger']) !!}
</div>
