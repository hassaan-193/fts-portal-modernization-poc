<?php

namespace App\DataTables;

use App\Models\Invoice;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class AmcInvoiceDataTable extends DataTable
{
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable
            ->addColumn('action', 'invoices.datatables_actions')
            ->addColumn('company_link', function ($query) {
                $quotation = $query->quotation;
                $company = $quotation->company ?? null;

                if (!$quotation || !$company) {
                    return '-';
                }

                $url = route('quotations.show', $quotation->id);
                $name = e($company->name);

                return '<a href="' . $url . '">' . $name . '</a>';
            })
            ->addColumn('status_tag', function ($query) {
                return view('components.datatables_status', [
                    'msg' => $query->status ? 'complete' : 'pending',
                    'type' => $query->status ? 'success' : 'danger',
                ]);
            })

            /**
             *  Filter per-column (for company name)
             */
            ->filterColumn('company_link', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                $query->whereHas('quotation.company', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                });
            })

            /**
             *  Global search: include company, quotation, and type names
             */
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $search = strtolower($search);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(invoices.invoice_no) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(invoices.amount) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(invoices.total_amount) LIKE ?', ["%{$search}%"])
                          ->orWhereHas('quotation.company', function ($sub) use ($search) {
                              $sub->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                          })
                          ->orWhereHas('quotation', function ($sub) use ($search) {
                              $sub->whereRaw('LOWER(ref_no) LIKE ?', ["%{$search}%"]);
                          })
                          ->orWhereHas('invoice_type', function ($sub) use ($search) {
                              $sub->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                          });
                    });
                }
            })

            //  Allow HTML links
        ->rawColumns(['company_link', 'status_tag', 'action']);
    }

    public function query(Invoice $model)
    {
        $query = $model->newQuery()
            ->with(['invoice_type', 'quotation.company'])
            ->whereHas('quotation', function ($q) {
                $q->whereRaw('TRIM(LOWER(category)) = "amc"');
            });

        if (request()->filled('remaining_filter') && request('remaining_filter') == '2_or_more') {
            $query->whereHas('quotation.invoices', function ($q) {
            }, '>=', 2);
        }

        if (request()->filled('status_filter')) {
            $status = request('status_filter') === 'complete' ? 1 : 0;
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'desc']],
                'buttons' => [
                    [
                        'extend' => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-download"></i> Export',
                    ],
                    [
                        'extend' => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-refresh"></i> Reload',
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
            'created_at' => new Column(['title' => 'Created At', 'data' => 'created_at']),
            'invoice_no' => new Column(['title' => 'Invoice No', 'data' => 'invoice_no']),
            'invoice_type_id' => new Column(['title' => 'Invoice Type', 'data' => 'invoice_type.name']),
            //  Updated Quotation column (Company link)
            'company_link' => new Column(['title' => 'Quotation (Company)', 'data' => 'company_link', 'name' => 'quotation.company.name']),
            'ref_no' => new Column(['title' => 'Ref No', 'data' => 'quotation.ref_no']),
            'amount' => new Column(['title' => 'Amount', 'data' => 'amount']),
            'total_amount' => new Column(['title' => 'Total Amount', 'data' => 'total_amount']),
            'status' => new Column(['title' => 'Status', 'data' => 'status_tag']),
        ];
    }

    protected function filename()
    {
        return 'amc_invoices_' . time();
    }
}
