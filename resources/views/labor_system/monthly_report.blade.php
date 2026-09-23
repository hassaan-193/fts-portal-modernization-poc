@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">labor attendance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">labor attendance</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">labor attendance Detail</h3>
        </div>
        <div class="card-body table-responsive" >

            <form method="GET" action="{{ route('labor.monthlyReport') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <label for="labor_id">Select Labor:</label>
        
                        <!-- Search input for filtering -->
                        <input type="text" id="labor_search" class="form-control" placeholder="Search Labor...">
                        
                        <!-- Dropdown with all labors initially visible -->
                        <select name="labor_id" id="labor_id" class="form-control" required>
                            @foreach($labors as $labor)
                                <option value="{{ $labor->id }}" {{ request('labor_id') == $labor->id ? 'selected' : '' }}>
                                    {{ $labor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
        
                    <div class="col-md-4">
                        <label for="month">Select Month:</label>
                        <input type="month" name="month" id="month" class="form-control"
                               value="{{ request('month', now()->format('Y-m')) }}">
                    </div>
        
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary" type="submit">View Report</button>
                    </div>
                </div>
            </form>
            
            <!-- Report Section -->
            @if(isset($reportData))
                <h5 class="mt-4">Report for: <strong>{{ $selectedLabor->name }}</strong> - {{ \Carbon\Carbon::parse(request('month'))->format('F Y') }}</h5>
                
                <!-- Total Overtime for the Month -->
                <div class="mt-4">
                    <strong>Total Overtime Hours for this Month: </strong>
                    <span>{{ $totalOvertimeHours }} hrs</span>
                </div>
                
                <!-- Overtime Report Table -->
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Overtime Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $entry)
                            <tr>
                                <td>{{ $entry->assignment_start_date }}</td>
                                <td>{{ $entry->project->subject ?? 'N/A' }}</td>
                                <td>{{ $entry->overtime_hours }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No records found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif



        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#labor_search').on('keyup', function() {
        var searchQuery = $(this).val().toLowerCase();
        $('#labor_id').show();
        $('#labor_id option').each(function() {
            var optionText = $(this).text().toLowerCase();
            if (optionText.indexOf(searchQuery) !== -1) {
                $(this).show();  // Show matching option
            } else {
                $(this).hide();  // Hide non-matching option
            }
        });
    });
    $('#labor_search').on('focusout', function() {
        if ($(this).val() === '') {
            $('#labor_id option').show();
        }
    });
    $('#labor_search').on('focus', function() {
        $('#labor_id').show();
    });
});
</script>
@endsection