@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Edit Inquiry</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Edit Inquiry #{{ $inquiry->inquiry_no }}</h3>
                </div>
                <div class="card-body">
                    {!! Form::model($inquiry, ['route' => ['inquiries.update', $inquiry->id], 'method' => 'PUT', 'files' => true]) !!}
                        @include('inquiries.fields')
                        <div class="mt-3">
                            {!! Form::submit('Update Inquiry', ['class' => 'btn btn-primary']) !!}
                            <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
