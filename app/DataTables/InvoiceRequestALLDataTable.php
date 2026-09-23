<?php

namespace App\DataTables;

use App\Models\InvoiceRequest;
use App\Models\Quotation;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InvoiceRequestALLDataTable extends DataTable
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

        return $dataTable->addColumn('action', function($query){
            if(!$query->status)
                return "<div class='btn-group' role='group'>
                    <a href='" . route('invoices.create',$query->id) ."' class='btn btn-sm btn-ghost-success' title='Approve Only'>
                        <i class='fa fa-plus'></i>
                    </a>
                    <a href='" . route('invoices.approve-email-form',$query->id) ."' class='btn btn-sm btn-ghost-primary' title='Approve & Send Email'>
                        <i class='fa fa-envelope'></i> Email
                    </a>
                </div>";
        })
        ->addColumn('quotation_model', function($query) {
            return view('components.datatables_relation_link', [
                'id' => $query->requestable->id,
                'name' => $query->requestable->name,
                'model' => 'quotations'
            ]);
        })
        ->addColumn('request_date', function($query) {
            return $query->getRequestTime();
        })
        ->addColumn('request_status', function($query){
            if($query->status)
                return '<small class="badge badge-success">completed</small>';
            else
                return '<small class="badge badge-danger">pending</small>';
        })
        ->rawColumns(['action','request_status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\InvoiceRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(InvoiceRequest $model)
    {
        $model = $model->newQuery()->with(['requestable.lpoins','user','requestable.quotation_type'])
                ->whereHasMorph('requestable', [Quotation::class])
                ->where('requestable_type' ,'App\Models\Quotation');

        if(request('status') && request('status') == 'pending')
            $model = $model->whereStatus(0);

        return $model->orderBy('id', 'desc')->get();
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
            ->ajax( [
                'url'=> url('/invoices/request/invoices'),
                'data'=> 'function(d){
                    d.status= $(\'input[name=status]\').val();
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
            'created_at' => new Column(['title' => __('models/invoice_requests.fields.created_at'), 'data' => 'request_date']),
            'quotation_id' => new Column(['title' => __('models/invoice_requests.fields.quotation_id'), 'data' => 'quotation_model' ]),
            'quotation_type_id' => new Column(['title' => __('models/quotations.fields.quotation_type_id'), 'data' => 'requestable.quotation_type.name']),
            'lpoin_id' => new Column(['title' => __('models/invoice_requests.fields.lpoin_id'), 'data' => 'requestable.lpoins.ref_no' ]),
            'requested_by' => new Column(['title' => __('models/users.singular'), 'data' => 'user.name','searchable' => true]),
            'amount' => new Column(['title' => __('models/invoice_requests.fields.amount'), 'data' => 'requestable.amount','searchable' => false]),
            'note' => new Column(['title' => __('models/invoice_requests.fields.note'), 'data' => 'note','width' => '20px', 'searchable' => false]),
            'status' => new Column(['title' => __('models/invoice_requests.fields.status'), 'data' => 'request_status','searchable' => true])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'invoice_requests_' . time();
    }
}
