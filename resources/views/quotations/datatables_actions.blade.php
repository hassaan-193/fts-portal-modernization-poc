{!! Form::open(['route' => ['quotations.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('quotations.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('quotations.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
    @if(!$status)
        <a href="{{ route('quotations.update_status', $id) }}"
            onclick="return confirm('Are you sure.')" data-toggle="tooltip" data-placement="top" title="Update Status" class='btn btn-warning-info'>
            <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
        </a>
    @else
        <a href="{{ route('quotations.update_status', $id) }}"
            onclick="return confirm('Are you sure.')" data-toggle="tooltip" data-placement="top" title="Update Status" class='btn btn-warning-info'>
            <i class="fas fa-ban"></i>
        </a>
    @endif
</div>
{!! Form::close() !!}
