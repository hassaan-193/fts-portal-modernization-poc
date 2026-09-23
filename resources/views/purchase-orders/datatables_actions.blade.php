{!! Form::open(['route' => ['purchase-orders.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('purchase-orders.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('purchase-orders.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    @if($model->status === 'Rejected')
    <a href="{{ route('purchase-orders.create', ['resubmit_from' => $id]) }}" class='btn btn-warning' title="Resubmit as a new request">
       <i class="fa fa-redo"></i>
    </a>
    @endif
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
