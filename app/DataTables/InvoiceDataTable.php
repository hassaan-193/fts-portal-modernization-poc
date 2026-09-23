<?php

namespace App\DataTables;

use App\Models\Invoice;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\DB;

class InvoiceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'invoices.datatables_actions')
        ->addColumn('model', function($query) {
            if (!$query->quotation) {
                return '-';
            }
            return view('components.datatables_relation_link', [
                'id' => $query->quotation->id,
                'name' => $query->quotation->name,
                'model' => 'quotations'
            ]);
        })
        ->addColumn('status_tag', function($query) {
            return view('components.datatables_status', [
                'msg' => ($query->status) ? 'complete' : 'pending',
                'type' => ($query->status) ? 'success' : 'danger',
            ]);
        })
        ->addColumn('company_name', function($query) {
            return $query->quotation->company->name ?? 'N/A';
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Invoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
        $model = $model->newQuery()->with(['invoice_type', 'quotation.company']);

        // Filter by status (pending only)
        if(request('status') && request('status') == 'pending'){
            $model->whereStatus(0);
        }

        // Filter 1: Companies with 2 or more PENDING invoices
        if(request('multiple_pending') == '1'){
            $model->where('status', 0) // Only pending invoices
                ->whereHas('quotation.company', function($q) {
                    $q->whereIn('id', function($subQuery) {
                        $subQuery->select('companies.id')
                            ->from('companies')
                            ->join('quotations', 'quotations.company_id', '=', 'companies.id')
                            ->join('invoices', 'invoices.quotation_id', '=', 'quotations.id')
                            ->where('invoices.status', 0) // Only count pending invoices
                            ->groupBy('companies.id')
                            ->havingRaw('COUNT(invoices.id) >= 2');
                    });
                });
        }

        // Filter 2: Search by company (show all PENDING invoices of that company)
        if(request('company_id') && request('company_id') != ''){
            $model->where('status', 0) // Only pending invoices
                ->whereHas('quotation.company', function($q) {
                    $q->where('id', request('company_id'));
                });
        }

        return $model->orderBy('created_at','desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax([
                'url'=> url('/invoices'),
                'data'=> 'function(d){
                    d.status = $(\'input[name=status]\').val();
                    d.multiple_pending = $(\'input[name=multiple_pending]:checked\').val();
                    d.company_id = $(\'select[name=company_id]\').val();
                }'
            ])
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                       'extend' => 'export',
                       'className' => 'btn btn-default btn-sm no-corner',
                       'text' => '<i class="fa fa-download"></i> ' .__('auth.app.export').''
                    ],
                    [
                       'extend' => 'reload',
                       'className' => 'btn btn-default btn-sm no-corner',
                       'text' => '<i class="fa fa-refresh"></i> ' .__('auth.app.reload').''
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'created_at' => new Column(['title' => __('models/invoices.fields.created_at'), 'data' => 'created_at','searchable' => false]),
            'invoice_no' => new Column(['title' => __('models/invoices.fields.invoice_no'), 'data' => 'invoice_no','searchable' => true]),
            'invoice_type_id' => new Column(['title' => __('models/invoices.fields.invoice_type_id'), 'data' => 'invoice_type.name','searchable' => true]),
            'company_name' => new Column(['title' => 'Company', 'data' => 'company_name', 'name' => 'quotation.company.name']),
            'quotation_id' => new Column(['title' => __('models/invoices.fields.quotation_id'), 'data' => 'model','name' => 'quotation.name']),
            'ref_no' => new Column(['title' => __('models/quotations.fields.ref_no'), 'data' => 'quotation.ref_no']),
            'start_date' => new Column(['title' => __('models/invoices.fields.start_date'), 'data' => 'start_date','searchable' => false]),
            'end_date' => new Column(['title' => __('models/invoices.fields.end_date'), 'data' => 'end_date','searchable' => false]),
            'amount' => new Column(['title' => __('models/invoices.fields.amount'), 'data' => 'amount','searchable' => false]),
            'vat' => new Column(['title' => __('models/invoices.fields.vat'), 'data' => 'vat','searchable' => false]),
            'total_amount' => new Column(['title' => __('models/invoices.fields.total_amount'), 'data' => 'total_amount','searchable' => false]),
            'status' => new Column(['title' => __('models/invoices.fields.status'), 'data' => 'status_tag','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'invoices_' . time();
    }
}