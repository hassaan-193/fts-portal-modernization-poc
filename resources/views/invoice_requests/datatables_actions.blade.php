{!! Form::open(['route' => ['invoiceRequests.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('invoiceRequests.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    @if(!$status)
        <a href="{{ route('invoiceRequests.edit', $id) }}" class='btn btn-info'>
        <i class="fa fa-edit"></i>
        </a>
        {!! Form::button('<i class="fa fa-trash"></i>', [
            'type' => 'submit',
            'class' => 'btn btn-danger',
            'onclick' => "return confirm('Are you sure?')"
        ]) !!}
    @endif
</div>
{!! Form::close() !!}
