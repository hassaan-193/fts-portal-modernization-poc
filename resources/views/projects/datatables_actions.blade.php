{!! Form::open(['route' => ['projects.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('projects.show', $id) }}" class='btn btn-success'>
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('projects.edit', $id) }}" class='btn btn-info'>
       <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('projects.get_extensions', $id) }}" data-toggle="tooltip" data-placement="top" title="Extension Details" class='btn btn-warning'>
        <i class="fa fa-file"></i>
    </a>
    {{-- <a href="{{ route('projects.get_lpoout', $id) }}" data-toggle="tooltip" data-placement="top" title="Lpoout Details" class='btn btn-warning'>
        <i class="fa fa-file"></i>
    </a> --}}
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
