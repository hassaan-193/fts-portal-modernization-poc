<div class='btn-group'>
    <a href="{{ route('inquiries.show', $inquiry->id) }}" class='btn btn-success btn-sm' title="View">
        <i class="fa fa-eye"></i>
    </a>
    @if(in_array($inquiry->status, ['New', 'Assigned']))
    <a href="{{ route('inquiries.department.create', $inquiry->id) }}" class='btn btn-primary btn-sm' title="Review & Assign">
        <i class="fa fa-check"></i>
    </a>
    @endif
</div>
