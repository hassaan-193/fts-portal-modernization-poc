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
            <form action="{{ route('attendance.updateGroup') }}" method="POST">
                @csrf
                <input type="hidden" name="project_id" value="{{ $projectId }}">
                <input type="hidden" name="assignment_start_date" value="{{ $assignmentStartDate }}">

                @foreach ($assignments as $assignment)
                    <div>
                        <strong>{{ $assignment->labor->name }}</strong><br>
                        <input type="hidden" name="assignments[{{ $loop->index }}][id]" value="{{ $assignment->id }}">
                        <input type="hidden" name="assignments[{{ $loop->index }}][hours_worked]" value="{{ $assignment->hours_worked }}" readonly><br>
                        Overtime Hours: <input type="number" name="assignments[{{ $loop->index }}][overtime_hours]" value="{{ $assignment->overtime_hours }}"><br><br>
                    </div>
                @endforeach

                <button type="submit">Update Group</button>
            </form>
        </div>
    </div>
</div>
@endsection


