{{-- POST, not DELETE: see the projects.deleteDraft route --}}
{!! Form::open(['route' => ['projects.deleteDraft', $id], 'method' => 'post']) !!}
<div class='btn-group'>
    <a href="{{ route('projects.viewReport', $id) }}" class='btn btn-success' title="View">
       <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('projects.editReport', $id) }}" class='btn btn-info' title="Continue editing">
       <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'title' => 'Delete draft',
        'onclick' => "return confirm('Delete this draft? This cannot be undone.')"
    ]) !!}
</div>
{!! Form::close() !!}
