<?php

namespace App\DataTables;

use App\Models\PurchaseOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PurchaseOrderAdminDataTable extends DataTable
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
                if ($row->status === 'Rejected') {
                    return '<span class="badge badge-danger">Rejected</span>';
                }
                return '<span class="badge badge-warning">Awaiting Approval</span>';
            })
            ->addColumn('company_name', function($row) {
                try {
                    // For 'project' type: get company through project -> quotation -> company
                    if ($row->request_type === 'project' && $row->project_id) {
                        if ($row->project && $row->project->quotation && $row->project->quotation->company) {
                            return $row->project->quotation->company->name;
                        }
                    }
                    
                    // For 'maintenance' or other types: get company through quotation -> company
                    if ($row->quotation_id) {
                        if ($row->quotation && $row->quotation->company) {
                            return $row->quotation->company->name;
                        }
                    }
                    
                    return 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('vendor_name', function($row) {
                try {
                    if ($row->lpout_vendor_id && $row->vendor) {
                        return $row->vendor->name;
                    }
                    return 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('total_amount_display', function($row) {
                try {
                    // Lump-sum pricing: the batch total is entered manually, not summed from rows
                    if (($row->lpout_pricing_mode ?? 'unit') === 'lump') {
                        return 'AED ' . number_format((float)($row->lpout_manual_total ?? 0), 2);
                    }

                    // Use lpout_items total if available, otherwise calculate from items
                    if ($row->lpout_items) {
                        $items = is_string($row->lpout_items) ? json_decode($row->lpout_items, true) : $row->lpout_items;
                        $total = 0;
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $total += (float)($item['total'] ?? 0);
                            }
                        }
                        return 'AED ' . number_format($total, 2);
                    }
                    
                    // Fallback to original items total_amount if available
                    if ($row->total_amount) {
                        return 'AED ' . number_format($row->total_amount, 2);
                    }
                    
                    return 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
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
                    foreach (PurchaseOrderDataTable::statusMatches($keyword) as $value) {
                        $q->orWhere('status', $value);
                    }
                });
            })
            ->filterColumn('total_amount_display', function($query, $keyword) {
                $number = preg_replace('/[^0-9.]/', '', $keyword);
                if ($number === '' || $number === '.') {
                    // No usable number in the keyword; match nothing for this column.
                    $query->whereRaw('1 = 0');
                    return;
                }
                $query->where(function($q) use ($number) {
                    $q->where('total_amount', 'like', "%{$number}%")
                        ->orWhere('lpout_manual_total', 'like', "%{$number}%");
                });
            })
            ->addColumn('action', 'purchase-orders.admin.datatables_actions')
            ->rawColumns(['action', 'status']);
    }

    public function query(PurchaseOrder $model)
    {
        return $model->newQuery()
            ->with(['project.quotation.company', 'quotation.company', 'vendor'])
            ->where('department_status', 'Approved')
            ->orderBy('id', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->addAction(['width' => '150px', 'printable' => false, 'title' => __('crud.action')])
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
            'total_amount_display' => new Column(['title' => 'Total Amount', 'data' => 'total_amount_display']),
            'date' => new Column(['title' => 'Date', 'data' => 'date']),
            'created_at' => new Column(['title' => 'Created At', 'data' => 'created_at']),
            'status' => new Column(['title' => 'Status', 'data' => 'status']),
        ];
    }

    protected function filename(): string
    {
        return 'po_admin_' . date('YmdHis');
    }
}
