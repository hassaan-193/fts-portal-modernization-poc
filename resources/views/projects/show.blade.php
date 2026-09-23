@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/projects.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('projects.index') !!}">@lang('models/projects.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        {{-- Include Project attributes --}}
        <div class="container-fluid">
            @include(strtolower(__('models/projects.plural')).'.show_fields')

            {{-- Linked Quotations Section --}}
            @php
                $linkedQuotations = \App\Models\Quotation::where('reference_project_id', $project->id)->get();
            @endphp
            @if ($linkedQuotations->count() > 0)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card card-maroon">
                            <div class="card-header">
                                <h3 class="card-title">Linked Quotations</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Quotation Name</th>
                                            <th>Ref No</th>
                                            <th>Type</th>
                                            <th>Company</th>
                                            <th>Amount (AED)</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($linkedQuotations as $quotation)
                                            <tr>
                                                <td>{{ $quotation->name }}</td>
                                                <td>{{ $quotation->ref_no ?? '—' }}</td>
                                                <td>{{ $quotation->quotation_type->name ?? '—' }}</td>
                                                <td>{{ $quotation->company->name ?? '—' }}</td>
                                                <td>{{ $quotation->total_amount ? number_format($quotation->total_amount, 2) : '—' }}</td>
                                                <td>{{ $quotation->date ? \Carbon\Carbon::parse($quotation->date)->format('d M Y') : '—' }}</td>
                                                <td>
                                                    <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($project->category === 'amc')
                <div class="row">
                    <div class="col-md-12">
                        <x-visit-schedule-card
                            :title="__('models/visit_schedules.plural')"
                            :visitSchedules="$visitSchedules"
                        />
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <x-CardTable :title="__('models/invoices.plural')" :project="$project->id" />
                </div>
            </div>
            <div class="row">

                <div class="col-md-6">
                    <!-- Project Lpoins -->
                    <x-CardTable :title="__('models/lpoins.plural')" :project="$project->id" />
                </div>
                <div class="col-md-6">
                    <!-- Project Lpoout -->
                    <x-CardTable :title="__('models/lpoouts.plural')" :project="$project->id" />
                </div>

                <div class="col-md-6">
                    <!-- Project Receipt -->
                    <x-CardTable :title="__('models/receipts.plural')" :project="$project->id" />
                </div>
                <div class="col-md-6">
                    <!-- Project Payment -->
                    <x-CardTable :title="__('models/payments.plural')" :project="$project->id" />
                </div>
                <div class="col-md-6">
                    <!-- Project Extentions -->
                    <x-CardTable :title="__('models/extensions.plural')" :project="$project->id" />
                </div>
                <div class="col-md-6">
                    <!-- Project PettyCash -->
                    <x-CardTable :title="__('models/petty_cashes.plural')" :project="$project->id" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include(strtolower(__('models/projects.plural')).'.projects_partials.comments')
                </div>
            </div>
        </div>
    </div>
@endsection
