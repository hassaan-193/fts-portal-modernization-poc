@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/projects.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/projects.plural')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/projects.plural') Detail</h3>
                <p>Status: {{ $visitSchedule->status }}</p>

            </div>
            <div class="card-body table-responsive">
                <!-- Back Button -->
                <a href="{{ route('projects.show', $visitSchedule->project->id) }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Back to Project Details
                </a>

                <!-- Display existing comments -->
                <ul class="list-group">
                    @foreach($comments as $comment)
                        <li class="list-group-item">
                            {{ $comment->comment }}

                            @if($visitSchedule->status !== 'done') <!-- Check if status is NOT 'done' -->
                                <!-- Edit Comment Button -->
                                <a href="{{ route('visit-schedules.edit-comment', [$visitSchedule->id, $comment->id]) }}" class="btn btn-warning btn-sm float-right mr-2">Edit</a>

                                <!-- Delete Comment Button -->
                                <form action="{{ route('visit-schedules.delete-comment', [$visitSchedule->id, $comment->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm float-right">Delete</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <!-- Only Show Add Comment Section if Status is NOT 'Done' -->
                @if($visitSchedule->status !== 'done') <!-- Check if status is NOT 'done' -->
                    <form action="{{ route('visit-schedules.store-comment', $visitSchedule->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="comment">Add Comment:</label>
                            <textarea name="comment" id="comment" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Add Comment</button>
                    </form>
                @else
                    <!-- Show message if status is 'done' -->
                    <div class="alert alert-info mt-3">
                        This project is marked as "Done". You can only view the comments.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
