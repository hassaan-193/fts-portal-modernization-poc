<?php

namespace App\DataTables;

use App\Models\Receipt;
use App\Models\Invoice;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class ReceiptDataTable extends DataTable
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
        ->addColumn('action', 'receipts.datatables_actions')
        ->addColumn('invoice_no', function($query){
            return $query->transactionable ? $query->transactionable->invoice_no : '';
        })
        ->addColumn('set_company', function($query) {
            if(isset($query->transactionable->quotation->company))
                return view('components.datatables_relation_link', [
                    'id' => $query->transactionable->quotation->company->id,
                    'name' => $query->transactionable->quotation->company->name,
                    'model' => 'companies'
                ]);
            elseif($query->account)
                return $query->account->type;
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
     * @param \App\Models\Receipt $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Receipt $model)
    {
        return $model->newQuery()->with('transaction_payment_type','transactionable.quotation.company','account')
            // ->whereHasMorph('transactionable', [Invoice::class])
            ->where('type','Receipt')->orderBy('id','desc')->get();
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
                    [
                        'extend' => 'colvis',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => __('auth.app.colvis')
                    ]
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
            'id' => new Column(['title' => __('models/receipts.fields.id'), 'data' => 'id','searchable' => false]),
            'date_time' => new Column(['title' => __('models/receipts.fields.date_time'), 'data' => 'date_time','searchable' => false]),
            'company_id' => new Column(['title' => __('models/receipts.fields.company_id'), 'data' => 'set_company','searchable' => true]),
            'invoice' => new Column(['title' => __('models/receipts.invoices_detail.no'), 'data' => 'invoice_no','searchable' => true]),
            'payment_type' => new Column(['title' => __('models/receipts.fields.payment_type'), 'data' => 'transaction_payment_type.name','searchable' => true]),
            'clearance_date' => new Column(['title' => __('models/receipts.fields.clearance_date'), 'data' => 'clearance_date','searchable' => true]),
            'payment_no' => new Column(['title' => __('models/receipts.fields.payment_no'), 'data' => 'payment_no','searchable' => true]),
            'total' => new Column(['title' => __('models/receipts.fields.total'), 'data' => 'total','searchable' => true]),
            'status' => new Column(['title' => __('models/receipts.fields.status'), 'data' => 'status_tag','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'receipts_' . time();
    }
}
