{!! Form::open(['route' => ['inquiries.destroy', $inquiry->id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('inquiries.show', $inquiry->id) }}" class='btn btn-success btn-sm' title="View">
        <i class="fa fa-eye"></i>
    </a>
    @if(auth()->user()->hasRole('Administration') || $inquiry->created_by === auth()->id())
    <a href="{{ route('inquiries.edit', $inquiry->id) }}" class='btn btn-info btn-sm' title="Edit">
        <i class="fa fa-edit"></i>
    </a>
    @endif
    @if(auth()->user()->hasRole('Administration'))
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-sm',
        'onclick' => "return confirm('Are you sure you want to delete this inquiry?')"
    ]) !!}
    @endif
</div>
{!! Form::close() !!}
