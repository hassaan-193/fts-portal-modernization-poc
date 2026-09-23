{!! Form::open(['route' => ['lpoins.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('invoiceRequests.create_with_lpoin', $id) }}" class='btn btn-info'>
        <i class="fa fa-plus"></i>
     </a>
    <a href="{{ route('lpoins.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('lpoins.edit', $id) }}" class='btn btn-warning'>
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
