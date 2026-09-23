<!-- resources/views/components/card-letters.blade.php -->

<div class="col-md-6">
    <!-- Display Letters -->
    <h5>{{ ucfirst($type) }} Letters</h5>
    @if($profile->letters->where('type', $type)->count() > 0)
        <div class="card">
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered" id="{{ $type }}LettersTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Issued By</th>
                            <th>Issued At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profile->letters->where('type', $type) as $letter)
                            <tr>
                                <td>{{ $letter->title }}</td>
                                <td>{{ $letter->issued_by }}</td>
                                <td>{{ \Carbon\Carbon::parse($letter->issued_at)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p>No {{ ucfirst($type) }} Letters available.</p>
    @endif
</div>

<script>
    $(document).ready(function() {
        $('#{{ $type }}LettersTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthChange": false,
            "pageLength": 5,
            "dom": '<"top"f>rt<"bottom"lp><"clear">', // Explicitly define search, length, and pagination
        });
    });
</script>

