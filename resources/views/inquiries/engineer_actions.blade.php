<div class='btn-group'>
    <a href="{{ route('inquiries.show', $inquiry->id) }}" class='btn btn-success btn-sm' title="View">
        <i class="fa fa-eye"></i>
    </a>
    @if(in_array($inquiry->status, ['Site Visit Pending', 'Site Visit Done']))
    <a href="{{ route('inquiries.engineer.create', $inquiry->id) }}" class='btn btn-primary btn-sm' title="Submit Report">
        <i class="fa fa-file"></i>
    </a>
    @endif
</div>
