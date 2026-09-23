<?php

namespace App\DataTables;

use App\Models\PurchaseOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PurchaseOrderDepartmentDataTable extends DataTable
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
            ->editColumn('status', function($row) {
                $ds = $row->department_status;
                if ($ds === 'Rejected') {
                    return '<span class="badge badge-danger">Rejected</span>';
                }
                if ($ds === 'Sent Back') {
                    return '<span class="badge badge-warning">Sent Back to Requester</span>';
                }
                if ($ds === 'Approved') {
                    // Forwarded to admin (Step 3) - once admin acts on it, reflect that decision here.
                    switch ($row->status) {
                        case 'Admin Approved':
                            return '<span class="badge badge-success">Admin Approved</span>';
                        case 'Rejected':
                            return '<span class="badge badge-danger">Admin Rejected</span>';
                        case 'Hold':
                            return '<span class="badge badge-secondary">On Hold</span>';
                        default:
                            return '<span class="badge badge-info">Forwarded to Admin</span>';
                    }
                }
                return '<span class="badge badge-warning">Pending Review</span>';
            })
            ->filterColumn('vendor_name', function ($query, $keyword) {
                $query->whereHas('vendor', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                $kw = strtolower(trim($keyword));
                $query->where(function ($q) use ($keyword, $kw) {
                    $q->where('status', 'like', "%{$keyword}%")
                        ->orWhere('department_status', 'like', "%{$keyword}%");

                    if ($kw === '') {
                        return;
                    }
                    if (strpos('forwarded to admin', $kw) !== false) {
                        $q->orWhere(function ($sub) {
                            $sub->where('department_status', 'Approved')
                                ->whereNotIn('status', ['Admin Approved', 'Rejected', 'Hold']);
                        });
                    }
                    if (strpos('admin approved', $kw) !== false) {
                        $q->orWhere(function ($sub) {
                            $sub->where('department_status', 'Approved')->where('status', 'Admin Approved');
                        });
                    }
                    if (strpos('admin rejected', $kw) !== false) {
                        $q->orWhere(function ($sub) {
                            $sub->where('department_status', 'Approved')->where('status', 'Rejected');
                        });
                    }
                    if (strpos('on hold', $kw) !== false) {
                        $q->orWhere(function ($sub) {
                            $sub->where('department_status', 'Approved')->where('status', 'Hold');
                        });
                    }
                    if (strpos('sent back to requester', $kw) !== false) {
                        $q->orWhere('department_status', 'Sent Back');
                    }
                    if (strpos('pending review', $kw) !== false) {
                        $q->orWhere(function ($sub) {
                            $sub->whereNotIn('department_status', ['Approved', 'Rejected', 'Sent Back'])
                                ->orWhereNull('department_status');
                        });
                    }
                });
            })
            ->filterColumn('company_name', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('project.quotation.company', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                    })->orWhereHas('quotation.company', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                    });
                });
            })
            ->addColumn('action', 'purchase-orders.department.datatables_actions')
            ->rawColumns(['action', 'status']);
    }

    public function query(PurchaseOrder $model)
    {
        return $model->newQuery()
            ->with(['project.quotation.company', 'quotation.company', 'vendor'])
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
            'company_name' => new Column(['title' => 'Company', 'data' => 'company_name']),
            'date' => new Column(['title' => 'Date', 'data' => 'date']),
            'created_at' => new Column(['title' => 'Created At', 'data' => 'created_at']),
            'status' => new Column(['title' => 'Status', 'data' => 'status']),
        ];
    }

    protected function filename(): string
    {
        return 'po_department_' . date('YmdHis');
    }
}
