{!! Form::open(['route' => ['staf_request_destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="#" data-id="{{ $id }}" data-toggle="modal" data-target="#statusModal" class='btn btn-primary'
        data-placement="top" title="Update Status">
        <i class="fa fa-edit" aria-hidden="true"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger',
        'onclick' => "return confirm('Are you sure?')",
    ]) !!}
</div>
{!! Form::close() !!}

<!-- Modal Structure -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Set Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form to select the status -->
                <form id="statusForm" action="#" method="POST">
                    @csrf
                    <!-- Select the status -->
                    <div class="form-group">
                        <label for="status">Choose Status</label>
                        <select class="form-control" name="status" id="status" required>
                            <option value="1">Approved</option>
                            <option value="2">Rejected</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- Submit the form -->
                <button type="submit" form="statusForm" class="btn btn-danger">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to update form action dynamically -->
<script>
    $('#statusModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id'); // Extract the id from data-* attribute

        // Update the form action with the dynamic id
        var actionUrl = "{{ url('staf') }}/request/" + id + "/update_status";
        $('#statusForm').attr('action', actionUrl);
    });
</script>
