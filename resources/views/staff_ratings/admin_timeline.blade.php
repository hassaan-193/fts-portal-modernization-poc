@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">Staff Rating Timeline</h1>
            </div>
            <div class="col-sm-6">
                <form method="GET" action="{{ route('staff-ratings.timeline') }}" class="form-inline float-sm-right">
                    <select name="month" class="form-control mr-2">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" class="form-control mr-2">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">All Ratings for {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h3>
        </div>

        <div class="card-body">
            {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endsection
