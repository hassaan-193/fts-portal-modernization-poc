<?php

namespace App\DataTables\CompanyCards;

use App\Models\Invoice;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class CompanyInvoiceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new CollectionDataTable($query);

        return $dataTable
        ->addColumn('status_tag', function($query) {
            return view('components.datatables_status', [
                'msg' => ($query->status) ? 'complete' : 'pending',
                'type' => ($query->status) ? 'success' : 'danger',
            ]);
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
        $model = $model->newQuery()->with('quotation','invoice_type');

        $model->whereHas('quotation',function($query){
            $query->whereId(\Auth::guard('company')->user()->id);
        });
        return $model->orderBy('created_at','desc')->get();
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
                'data'=> 'function(d){
                    d.table= "companyInvoiceDataTable";
                }'
            ])
            ->parameters([
                // 'dom'       => 'Bfrtip',
                'dom'       => '<"row" <"col-md-8"B> <"col-md-4"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort' => false,
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
            'invoice_no' => new Column(['title' => __('models/invoices.fields.invoice_no'), 'data' => 'invoice_no','searchable' => true]),
            'invoice_type_id' => new Column(['title' => __('models/invoices.fields.invoice_type_id'), 'data' => 'invoice_type.name','searchable' => true]),
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
