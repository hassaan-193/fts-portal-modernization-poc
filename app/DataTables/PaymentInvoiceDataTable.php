<?php

namespace App\DataTables;

use App\Models\PaymentInvoice;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PaymentInvoiceDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'payment_invoices.datatables_actions')
        ->addColumn('model', function($query) {
            if(isset($query->lpoout))
                return view('components.datatables_relation_link', [
                    'id' => $query->lpoout->id,
                    'name' => $query->lpoout->name,
                    'model' => 'lpoouts'
                ]);
            else
                return '';
        })
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
     * @param \App\Models\PaymentInvoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PaymentInvoice $model)
    {
        return $model->newQuery()->with('lpoout')->orderBy('id','desc');;
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
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
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
                    [
                       'extend' => 'create',
                       'className' => 'btn btn-default btn-sm no-corner',
                       'text' => '<i class="fa fa-plus"></i> ' .__('auth.app.create').''
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
            'type' => new Column(['title' => __('models/payment_invoices.fields.type'), 'data' => 'type']),
            'invoice_no' => new Column(['title' => __('models/payment_invoices.fields.invoice_no'), 'data' => 'invoice_no']),
            'lpoout_id' => new Column(['title' => __('models/payment_invoices.fields.lpoout_id'), 'data' => 'model', 'name' => 'lpoout.name']),
            'start_date' => new Column(['title' => __('models/payment_invoices.fields.start_date'), 'data' => 'start_date','searchable' => false]),
            'end_date' => new Column(['title' => __('models/payment_invoices.fields.end_date'), 'data' => 'end_date','searchable' => false]),
            'amount' => new Column(['title' => __('models/payment_invoices.fields.amount'), 'data' => 'total_amount', 'searchable' => false]),
            'status' => new Column(['title' => __('models/payment_invoices.fields.status'), 'data' => 'status_tag','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'payment_invoices_' . time();
    }
}
