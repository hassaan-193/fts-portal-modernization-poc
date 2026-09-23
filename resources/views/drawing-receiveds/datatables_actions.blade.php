{!! Form::open(['route' => ['drawing-receiveds.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('drawing-receiveds.show', $id) }}" class='btn btn-success' title="View">
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('drawing-receiveds.edit', $id) }}" class='btn btn-warning' title="Edit">
       <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('drawing-receiveds.contributions', $id) }}" class='btn btn-info' title="Contributions">
       <i class="fa fa-history"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'title' => 'Delete',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
