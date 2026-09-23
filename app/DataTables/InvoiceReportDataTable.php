<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Invoice;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class InvoiceReportDataTable extends DataTable
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

        return $dataTable->addColumn('model', function($query) {
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
        ->addColumn('quotation_contract_value', function($query) {
            return $query->quotation->amount;
        })
        ->addColumn('set_net', function($query) {
            return $query->total_amount - $query->vat - $query->invoice_service_details->sum('amount') ;
        })
        ->addColumn('setGorvementFee', function($query) {
            return $query->invoice_service_details->sum('amount');
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Invoice $model
     * @return \Illuminate\Database\Collection\Builder
     */
    public function query(Invoice $model)
    {
        $model = $model->newQuery()
            ->with('quotation.company:id,name,vat_no','invoice_type:id,name','quotation.lpoins','invoice_service_details','request.user')
            ->whereHas('quotation')
            ->orderBy('id','desc');

        if(request('from_date') && request('to_date'))
            $model->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        if(request('status')!=null)
            $model->where('status','=',request('status'));

        return $model->orderBy('id','desc')->get();
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
                'url'=> url('reports/invoice') ,
                'data'=> 'function(d){
                    d.name= $(\'input[name=name]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                    d.status= $(\'input[name=status]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort' => false,
                'order'     => [[0, 'asc']],
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
                        'extend' => 'colvis',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => __('auth.app.colvis')
                    ]
                ],
                "fnDrawCallback" => "function(row, data, start, end, display) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    net = api
                        .column( 11 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    vat = api
                        .column( 12 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    gov = api
                        .column( 13)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    total_amount = api
                        .column( 14 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 10 ).footer() ).html('Total:' );
                    $( api.column( 11 ).footer() ).html(net.toFixed(2));
                    $( api.column( 12 ).footer() ).html(vat.toFixed(2));
                    $( api.column( 13 ).footer() ).html(gov.toFixed(2));
                    $( api.column( 14 ).footer() ).html(total_amount.toFixed(2));
                }",
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
            'id' => new Column(['title' => __('models/invoices.fields.invoice_no'), 'data' => 'id','searchable' => true]),
            'invoice_no' => new Column(['title' => __('models/invoices.fields.invoice_no'), 'data' => 'invoice_no','searchable' => true]),
            'invoice_type_id' => new Column(['title' => __('models/invoices.fields.invoice_type_id'), 'data' => 'invoice_type.name','searchable' => true]),
            'quotation_id' => new Column(['title' => __('models/invoices.fields.quotation_id'), 'data' => 'model','name' => 'quotation.name','searchable' => true]),
            'company_name' => new Column(['title' => __('models/invoices.fields.company_id'), 'data' => 'quotation.company.name','searchable' => true]),
            'comapny_vat_no' => new Column(['title' => __('models/invoices.fields.company_vat_no'), 'data' => 'quotation.company.vat_no']),
            'lpoin' => new Column(['title' => __('models/invoices.fields.lpoin_id'), 'data' => 'quotation.lpoins.ref_no']),
            'ref_no' => new Column(['title' => __('models/quotations.fields.ref_no'), 'data' => 'quotation.ref_no']),
            'start_date' => new Column(['title' => __('models/invoices.fields.start_date'), 'data' => 'start_date','searchable' => false]),
            'requested_by' => new Column(['title' => __('models/users.singular'), 'data' => 'request.user.name','searchable' => true]),
            'quotation_contract_value' => new Column(['title' => __('models/quotations.fields.amount'), 'data' => 'quotation_contract_value','searchable' => false]),
            'amount' => new Column(['title' => __('models/invoices.fields.amount'), 'data' => 'set_net','searchable' => false]),
            'vat' => new Column(['title' => __('models/invoices.fields.vat'), 'data' => 'vat','searchable' => false]),
            'government_fee' => new Column(['title' => __('models/lpoins.fields.government_fee'), 'data' => 'setGorvementFee']),
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
