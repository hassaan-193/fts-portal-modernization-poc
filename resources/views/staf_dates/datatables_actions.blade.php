{!! Form::open(['route' => ['staf-dates.destroy', $id], 'method' => 'delete', 'style' => 'display:inline']) !!}
<div class='btn-group'>
    <a href="{{ route('staf-dates.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('staf-dates.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure you want to delete this leave period?')"
    ]) !!}
</div>
{!! Form::close() !!}
