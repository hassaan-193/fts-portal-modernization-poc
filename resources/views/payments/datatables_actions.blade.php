{!! Form::open(['route' => ['payments.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('payments.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    @if(!$status)
        <a href="{{ route('payments.edit', $id) }}" class='btn btn-info'>
            <i class="fa fa-edit"></i>
        </a>
    @endif
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
