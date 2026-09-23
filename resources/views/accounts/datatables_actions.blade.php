{!! Form::open(['route' => ['accounts.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('accounts.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('accounts.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
