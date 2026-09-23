{!! Form::open(['route' => ['users.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group btn-group-sm'>
    <a href="{{ route('users.edit',$id) }}"  class='btn btn-success'>
        <i class="fas fa-pen"></i>
     </a>
    @if($id != 1)
        {!! Form::button('<i class="fas fa-trash"></i>', [
            'type' => 'submit',
            'class' => 'btn btn-danger',
            'onclick' => "return confirm('Are you sure?')"
        ]) !!}
    @endif
</div>
{!! Form::close() !!}
