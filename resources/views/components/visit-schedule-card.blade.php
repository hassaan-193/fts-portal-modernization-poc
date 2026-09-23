<style>
    .pending-row {
        background-color: #f8d7da; 
    }
    .done-row {
        background-color: #d4edda; 
    }
    .upcoming-row {
        background-color: #fff3cd;
    }
</style>

<div class="card">
    <div class="card-header">
            <h3 class="card-title">
                Project Name:{{ $visitSchedules->first()->project->subject ?? 'N/A' }}
            </h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Visit Date</th>
                    <th>Status</th>
                    <th>Report</th>
                    <th>file uploaded</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visitSchedules as $schedule)
                    <tr class="
                        @if($schedule->status === 'pending')
                            pending-row
                        @elseif($schedule->status === 'done')
                            done-row
                        @elseif($schedule->status === 'upcoming')
                            upcoming-row
                        @endif
                    ">
                        <td>{{ $schedule->visit_date }}</td>
                        <td>{{ ucfirst($schedule->status) }}</td>
                        <td>
                            @if ($schedule->projectReport)
                                <a href="{{ route('projects.viewReport', $schedule->projectReport->id) }}" >View Report</a>
                            @else
                                No Approved Report
                            @endif
                        </td>
                        <td>
                            @if ($schedule->file_uploaded)
                                <a href="{{ asset('storage/' . $schedule->file_path) }}" target="_blank">View File</a>
                            @else
                                No file uploaded
                            @endif
                        </td>
                        <td>
                            @if($schedule->status !== 'done')
                                <form action="{{ route('visit-schedules.upload-file', $schedule->id) }}" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                                    @csrf
                                    <input type="file" name="file" style="display: inline;" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                            @if($schedule->status !== 'done')
                                <form action="{{ route('visit-schedules.update-status', $schedule->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success btn-sm">Mark as Done</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('visit-schedules.comments', $schedule->id) }}" class="btn btn-info btn-sm">Manage Comments</a>
                        </td>
                        
                        

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
