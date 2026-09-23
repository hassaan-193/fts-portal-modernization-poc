@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">
                        @lang('crud.detail') @lang('models/lpoouts.singular')
                        @if($lpoout->revision_number > 1)
                            <span class="badge badge-info ml-2">Rev #{{ $lpoout->revision_number }}</span>
                        @endif
                        @if(!$lpoout->is_latest_revision)
                            <span class="badge badge-secondary ml-1">Archived</span>
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('lpoouts.index') !!}">@lang('models/lpoouts.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        @if(!$lpoout->is_latest_revision)
        <div class="alert alert-warning">
            <i class="fa fa-archive mr-1"></i>
            <strong>Archived Revision</strong> — This is revision #{{ $lpoout->revision_number }} of LPO {{ $lpoout->lpo_invoice_no }}.
            It has been superseded and is preserved for audit purposes only. Payments cannot be made against this version.
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">

                <ul class="nav nav-tabs" id="lpooutTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-details">LPO Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-revisions">
                            Revision History
                            @if($revisionCount > 1)
                                <span class="badge badge-secondary">{{ $revisionCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                <div class="tab-content bg-white border border-top-0 p-3">

                    <div class="tab-pane fade show active" id="tab-details">
                        <div class="card-header px-0">
                            <h3 class="card-title">@lang('models/lpoouts.singular') Details</h3>
                        </div>
                        <div class="card-body px-0">
                            <ul class="list-group list-group-unbordered mb-3">
                                @include(strtolower(__('models/lpoouts.plural')).'.show_fields')
                            </ul>
                            <div class="mt-3">
                                <a href="{{ route('lpoouts.print', $lpoout->id) }}" target="_blank" class="btn btn-secondary mr-1">
                                    <i class="fa fa-print"></i> Print LPO
                                </a>
                                <a href="{{ route('lpoouts.downloadPdf', $lpoout->id) }}" class="btn btn-primary mr-1">
                                    <i class="fa fa-file-pdf-o"></i> Download Merged PDF
                                </a>

                                @if($lpoout->is_latest_revision && $lpoout->canBeRevised())
                                <a href="{{ route('lpoouts.showRevise', $lpoout->id) }}" class="btn btn-warning mr-1">
                                    <i class="fa fa-code-fork"></i> Revise LPO
                                </a>
                                @elseif($lpoout->is_latest_revision && !$lpoout->canBeRevised())
                                <button class="btn btn-warning mr-1" disabled
                                    title="Cannot revise: a payment invoice has been created against this LPO.">
                                    <i class="fa fa-lock"></i> Revise LPO
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body px-0">
                            @include('components.model_files',['model' => $lpoout ])
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-revisions">
                        <div class="bg-white card-primary card-maroon mt-3">
                            <div class="card-header">
                                <h3 class="card-title">All Revisions of {{ $lpoout->lpo_invoice_no }}</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="tblRevisions" class="table table-hover table-bordered table-striped table-sm text-nowrap" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Revision</th>
                                            <th>Status</th>
                                            <th>Amount (AED)</th>
                                            <th>Date</th>
                                            <th>Revised By</th>
                                            <th>Reason</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allRevisions as $rev)
                                        <tr class="{{ $rev->id === $lpoout->id ? 'table-primary' : '' }}">
                                            <td>
                                                #{{ $rev->revision_number }}
                                                @if(!$rev->is_latest_revision)
                                                    <span class="badge badge-secondary ml-1">Archived</span>
                                                @else
                                                    <span class="badge badge-success ml-1">Current</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $rev->status === 'Approved' ? 'success' : ($rev->status === 'Not Approved' ? 'danger' : 'warning') }}">
                                                    {{ $rev->status }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($rev->total_amount, 2) }}</td>
                                            <td>{{ $rev->date ? \Carbon\Carbon::parse($rev->date)->format('d M Y') : '—' }}</td>
                                            <td>{{ optional($rev->revisedByUser)->name ?? ($rev->revision_number === 1 ? 'Original' : '—') }}</td>
                                            <td>{{ $rev->revision_reason ?? ($rev->revision_number === 1 ? 'Initial creation' : '—') }}</td>
                                            <td>{{ $rev->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                @if($rev->id !== $lpoout->id)
                                                <a href="{{ route('lpoouts.show', $rev->id) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                @else
                                                <span class="text-muted small">Viewing</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    <script>
    $(function () {
        $('#tblRevisions').DataTable({
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excel', className: 'btn btn-default btn-sm no-corner', text: '<i class="fa fa-download"></i> Export' },
                { extend: 'reload', className: 'btn btn-default btn-sm no-corner', text: '<i class="fa fa-refresh"></i> Reload' }
            ],
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: -1 }],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json' }
        });

        // Re-draw when the tab becomes visible (DataTables need this inside hidden tabs)
        $('a[href="#tab-revisions"]').on('shown.bs.tab', function () {
            $('#tblRevisions').DataTable().columns.adjust().draw(false);
        });
    });
    </script>
@endsection
