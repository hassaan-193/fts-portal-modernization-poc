{!! Form::open(['route' => ['letters.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('letters.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}



