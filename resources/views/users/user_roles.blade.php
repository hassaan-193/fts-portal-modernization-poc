<div class='btn-group btn-group-sm'>
    @foreach($roles as $role)
        <a href="{{ route('roles.edit',$role['id']) }}"  class='btn btn-link'>
            {{ $role['name'] }}
        </a>
    @endforeach
</div>
