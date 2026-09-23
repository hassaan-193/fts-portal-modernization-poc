<?php

namespace App\DataTables;

use App\Models\InvoiceRequest;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InvoiceRequestDataTable extends DataTable
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
            ->addColumn('action', 'invoice_requests.datatables_actions')
            ->addColumn('quotation_model', function($query) {
                if($query->requestable)
                    return view('components.datatables_relation_link', [
                        'id' => $query->requestable->id,
                        'name' => $query->requestable->name,
                        'model' => 'quotations'
                    ]);
                return "";
            })
            ->addColumn('request_date', function($query) {
                return $query->getRequestTime();
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
     * @param \App\Models\InvoiceRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(InvoiceRequest $model)
    {
        $model = $model->newQuery()->with('requestable');

        // Apply user role filter
        if (!auth()->user()->hasRole([1])) {
            $model->where('user_id', auth()->user()->id);
        }

        // Apply date range filter
        if (request()->has(['from_date', 'to_date']) && request('from_date') && request('to_date') && !request()->get('search')['value']) {
            $model->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        }

        // Apply status filter
        if(request('status') !=null ){
            $model->where('status', '=', request('status'));
        }

        // Order and return
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
            ->ajax([
                'url'=> url('/invoiceRequests') ,
                'data'=> 'function(d){
                    d.status= $(\'input[name=request_status]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
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
            'quotation_id' => new Column(['title' => __('models/invoice_requests.fields.quotation_id'), 'data' => 'quotation_model',]),
            'note' => new Column(['title' => __('models/invoice_requests.fields.note'), 'data' => 'note','searchable' => false]),
            'status' => new Column(['title' => __('models/invoice_requests.fields.status'), 'data' => 'status_tag','searchable' => false]),
            'created_at' => new Column(['title' => __('models/invoice_requests.fields.created_at'), 'data' => 'request_date']),
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
