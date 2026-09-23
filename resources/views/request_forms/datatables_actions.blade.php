{!! Form::open(['route' => ['requestForms.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('requestForms.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('requestForms.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
