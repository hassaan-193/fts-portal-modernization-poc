@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">
                        Edit Leave Period
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staf-dates.index') }}">Staff Dates</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        <div class="card card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Edit Leave Period</h3>
            </div>

            {!! Form::open(['route' => ['staf-dates.update', $stafDate->id], 'method' => 'patch']) !!}
            <div class="card-body">
                <div class="row">
                    @include('staf_dates.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-flat']) !!}
                <a href="{{ route('staf-dates.index') }}" class="btn btn-secondary btn-flat">Cancel</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
