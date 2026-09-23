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
            </div>
            <div class="card-body table-responsive">
                <!-- Edit comment form -->
                <form action="{{ route('visit-schedules.update-comment', [$visitSchedule->id, $comment->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="comment">Edit Comment:</label>
                        <textarea name="comment" id="comment" class="form-control" required>{{ $comment->comment }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Update Comment</button>
                </form>
            </div>
        </div>
    </div>
@endsection
