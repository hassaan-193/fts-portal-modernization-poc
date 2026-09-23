@if($status === 'pending')
    <form method="POST" action="{{ route('pending.attendance.approve', $id) }}" class="form-inline d-flex gap-2" style="flex-wrap: wrap;">
        @csrf
        
        <!-- Informed/Uninformed Select -->
        <select name="informed" class="form-control form-control-sm informed-select" data-id="{{ $id }}" required onchange="toggleReasonSelect(this, {{ $id }})">
            <option value="">Inform Status...</option>
            <option value="informed">Informed</option>
            <option value="uninformed">Uninformed</option>
        </select>
        
        <!-- Reason Select (Hidden by default) -->
        <select name="specific_reason" id="reason-select-{{ $id }}" class="form-control form-control-sm d-none" data-id="{{ $id }}">
            <option value="">Select Reason...</option>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Emergency">Emergency</option>
            <option value="Personal Reasons">Personal Reasons</option>
        </select>
        
        <!-- Approve Button -->
        <button type="submit" class="btn btn-sm btn-success">
            <i class="fa fa-check"></i> Approve
        </button>
        
        <!-- Reject Button (Different form) -->
        <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Are you sure you want to reject?')) { document.getElementById('reject-form-{{ $id }}').submit(); }">
            <i class="fa fa-times"></i> Not Granted
        </button>
    </form>
    
    <!-- Hidden Reject Form -->
    <form id="reject-form-{{ $id }}" method="POST" action="{{ route('pending.attendance.reject', $id) }}" style="display: none;">
        @csrf
        <input type="hidden" name="reason" value="Rejected by manager">
        <input type="hidden" name="informed" value="uninformed">
    </form>
@else
    <span class="badge badge-secondary">{{ ucfirst($status) }}</span>
@endif

<script>
function toggleReasonSelect(selectElement, id) {
    const reasonSelect = document.getElementById(`reason-select-${id}`);
    if (selectElement.value === 'informed') {
        reasonSelect.classList.remove('d-none');
        reasonSelect.required = true;
    } else {
        reasonSelect.classList.add('d-none');
        reasonSelect.value = '';
        reasonSelect.required = false;
    }
}
</script>
