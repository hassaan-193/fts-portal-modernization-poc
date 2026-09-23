<?php

namespace App\DataTables;

use App\Models\PurchaseOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PurchaseOrderDataTable extends DataTable
{
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addIndexColumn()
            ->editColumn('date', function($row) {
                return $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : 'N/A';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') : 'N/A';
            })
            ->editColumn('status', function($row) {
                if ($row->status === 'Admin Approved') {
                    return '<span class="badge badge-success">Admin Approved</span>';
                }
                if ($row->status === 'Approved') {
                    return '<span class="badge badge-info">Awaiting Admin Approval</span>';
                }
                if ($row->status === 'Rejected') {
                    return '<span class="badge badge-danger">Rejected</span>';
                }
                if ($row->status === 'Sent Back') {
                    return '<span class="badge badge-warning">Sent Back - Needs Correction</span>';
                }
                if ($row->status === 'Hold') {
                    return '<span class="badge badge-secondary">On Hold</span>';
                }
                return '<span class="badge badge-warning">Awaiting Dept. Approval</span>';
            })
            ->addColumn('company_name', function($row) {
                if ($row->quotation && $row->quotation->company) {
                    return $row->quotation->company->name;
                }
                if ($row->project && $row->project->quotation && $row->project->quotation->company) {
                    return $row->project->quotation->company->name;
                }
                return '—';
            })
            ->addColumn('vendor_name', function($row) {
                return $row->vendor ? $row->vendor->name : '—';
            })
            ->filterColumn('vendor_name', function($query, $keyword) {
                $query->whereHas('vendor', function($sub) use ($keyword) {
                    $sub->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('company_name', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->whereHas('quotation.company', function($sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('project.quotation.company', function($sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->filterColumn('status', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('status', 'like', "%{$keyword}%");
                    foreach (self::statusMatches($keyword) as $value) {
                        $q->orWhere('status', $value);
                    }
                });
            })
            ->addColumn('action', 'purchase-orders.datatables_actions')
            ->rawColumns(['action', 'status']);
    }

    public function query(PurchaseOrder $model)
    {
        return $model->newQuery()->with(['vendor', 'quotation.company', 'project.quotation.company'])->orderBy('id', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('purchaseOrdersTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '160px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'bSort' => false,
                'order' => [[0, 'desc']],
                'buttons' => [
                    [
                        'extend' => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-download"></i> ' . __('auth.app.export')
                    ],
                    [
                        'extend' => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload')
                    ],
                    [
                        'extend' => 'create',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-plus"></i> ' . __('auth.app.create')
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    protected function getColumns()
    {
        return [
            'DT_RowIndex' => new Column(['title' => 'No.', 'data' => 'DT_RowIndex', 'searchable' => false, 'orderable' => false]),
            'request_number' => new Column(['title' => 'Request Number', 'data' => 'request_number']),
            'request_type' => new Column(['title' => 'Request Type', 'data' => 'request_type']),
            'vendor_name' => new Column(['title' => 'Vendor', 'data' => 'vendor_name']),
            'company_name' => new Column(['title' => 'Company', 'data' => 'company_name']),
            'date' => new Column(['title' => 'Date', 'data' => 'date']),
            'created_at' => new Column(['title' => 'Created At', 'data' => 'created_at']),
            'status' => new Column(['title' => 'Status', 'data' => 'status']),
        ];
    }

    /**
     * Map a search keyword against the human-readable status labels shown in the
     * table so a search for e.g. "awaiting admin" matches the stored value.
     */
    public static function statusMatches(string $keyword): array
    {
        $kw = strtolower(trim($keyword));

        if ($kw === '') {
            return [];
        }

        $labels = [
            'admin approved'          => ['Admin Approved'],
            'awaiting admin approval' => ['Approved', 'Pending Admin Approval'],
            'awaiting approval'       => ['Approved', 'Pending Admin Approval'],
            'rejected'                => ['Rejected'],
            'sent back'               => ['Sent Back'],
            'needs correction'        => ['Sent Back'],
            'on hold'                 => ['Hold'],
            'awaiting dept. approval' => ['Pending'],
            'awaiting dept approval'  => ['Pending'],
            'pending'                 => ['Pending', 'Pending Admin Approval'],
        ];

        $matches = [];
        foreach ($labels as $label => $values) {
            if (strpos($label, $kw) !== false) {
                $matches = array_merge($matches, $values);
            }
        }

        return array_values(array_unique($matches));
    }

    protected function filename(): string
    {
        return 'purchase-orders_' . date('YmdHis');
    }
}
