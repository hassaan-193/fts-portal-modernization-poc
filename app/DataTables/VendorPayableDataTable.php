<?php

namespace App\DataTables;

use App\Models\PaymentInvoice;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class VendorPayableDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                $actions = '<a href="' . route('vendor-payables.show', $row->id) . '" class="btn btn-xs btn-info mr-1"><i class="fa fa-eye"></i></a>';

                if ($row->status === 0) {
                    $actions .= '<a href="' . route('vendor-payables.edit', $row->id) . '" class="btn btn-xs btn-warning mr-1"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('vendor-payables.cancel', $row->id) . '" method="POST" style="display:inline"
                        onsubmit="return confirm(\'Cancel this payable? It will be removed from the payment queue.\')">
                        ' . csrf_field() . method_field('PATCH') . '
                        <button class="btn btn-xs btn-secondary mr-1" title="Cancel"><i class="fa fa-ban"></i></button>
                    </form>';
                }

                return $actions;
            })
            ->addColumn('vendor_name', fn($r) => optional($r->vendor)->name ?? $r->historical_party ?? '—')
            ->addColumn('project_name', fn($r) => optional($r->project)->subject ?? $r->historical_project ?? '—')
            ->addColumn('created_by_name', fn($r) => optional($r->createdBy)->name ?? '—')
            ->addColumn('status_badge', function ($r) {
                if ($r->status === 1) {
                    return '<span class="badge badge-success">Paid</span>';
                } elseif ($r->status === -1) {
                    return '<span class="badge badge-secondary">Cancelled</span>';
                }
                $due = $r->source_date;
                if ($due && $due->isPast()) {
                    return '<span class="badge badge-danger">Overdue</span>';
                }
                return '<span class="badge badge-warning">Pending</span>';
            })
            ->addColumn('due_date', fn($r) => $r->source_date ? $r->source_date->format('d M Y') : '—')
            ->rawColumns(['action', 'status_badge']);
    }

    public function query(PaymentInvoice $model)
    {
        return $model->newQuery()
            ->where('is_historical', true)
            ->with(['vendor', 'project', 'createdBy'])
            ->orderBy('id', 'desc')
            ->select('payment_invoices.*');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '100px', 'printable' => false, 'title' => 'Action'])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'asc']],
                'buttons'   => [
                    ['extend' => 'export',  'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-download"></i> Export'],
                    ['extend' => 'reload',  'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-refresh"></i> Reload'],
                    ['extend' => 'create',  'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-plus"></i> Add Payable'],
                ],
                'language' => ['url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json')],
            ]);
    }

    protected function getColumns()
    {
        return [
            'due_date'       => new Column(['title' => 'Due Date',     'data' => 'due_date',        'name' => 'source_date']),
            'vendor'         => new Column(['title' => 'Vendor',       'data' => 'vendor_name',     'name' => 'vendor.name']),
            'project'        => new Column(['title' => 'Project',      'data' => 'project_name',    'name' => 'project.subject', 'searchable' => false]),
            'invoice_no'     => new Column(['title' => 'Reference No', 'data' => 'invoice_no']),
            'total_amount'   => new Column(['title' => 'Amount (AED)', 'data' => 'total_amount',    'searchable' => false]),
            'status'         => new Column(['title' => 'Status',       'data' => 'status_badge',    'name' => 'status', 'searchable' => false]),
            'created_by'     => new Column(['title' => 'Created By',   'data' => 'created_by_name', 'name' => 'createdBy.name', 'searchable' => false]),
        ];
    }

    protected function filename()
    {
        return 'vendor_payables_' . time();
    }
}
