<?php

namespace App\DataTables;

use App\Models\Receipt;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ReceiptReportDataTable extends DataTable
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
        return $dataTable->addColumn('setGorvementFee', function($query) {
            return ($query->transactionable) ? $query->transactionable->invoice_service_details->sum('amount') : 0 ;
        })
        ->addColumn('setNetAmount', function($query) {
            return ($query->transactionable) ? $query->transactionable->amount - $query->transactionable->invoice_service_details->sum('amount') : 0 ;
        })
        ->addColumn('set_start_date', function($query) {
            return ($query->transactionable) ? $query->transactionable->start_date : '' ;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Account $model
     * @return \Illuminate\Database\Collection\Builder
     */
    public function query(Receipt $model)
    {
        $model = $model->newQuery()->with([
            'account:id,type' ,
            'transactionable:id,invoice_no,quotation_id,vat,amount,total_amount,created_at,start_date',
            'transactionable.quotation:id,name,company_id',
            'transactionable.quotation.company:id,name,vat_no',
            'transactionable.quotation.lpoins:id,ref_no',
            'transaction_payment_type',
            ])
            ->select('id','date_time','transactionable_id','transactionable_type','account_id','transaction_type','payment_type','clearance_date','payment_no','created_at')
            ->where('type','Receipt')
            ->whereNotNull('transactionable_id');

        if( (request('from_date') != '' && request('to_date') != '') && !request()->get('search')['value'] ){
            $model->whereDate('date_time', '>=', request('from_date'))
                ->whereDate('date_time', '<=', request('to_date'));
        }
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
                'url'=> url('reports/receipt') ,
                'data'=> 'function(d){
                    d.name= $(\'input[name=name]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
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
                        .column( 13 )
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
                }"
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
            'id' => new Column(['title' => __('models/reports.receipt.id'), 'data' => 'id','searchable' => false]),
            'date' => new Column(['title' => __('models/reports.receipt.date'), 'data' => 'date_time', 'searchable' => false]),
            'voucher' => new Column(['title' => __('models/reports.receipt.voucher'), 'data' => 'transactionable.invoice_no']),
            'start_date' => new Column(['title' => __('models/reports.receipt.created_at'), 'data' => 'set_start_date', 'searchable' => false]),
            'quotation' => new Column(['title' => __('models/reports.receipt.quotation'), 'data' => 'transactionable.quotation.name','searchable' => true]),
            'lpoin' => new Column(['title' => __('models/reports.receipt.lpo_in'), 'data' => 'transactionable.quotation.lpoins.ref_no']),
            'company' => new Column(['title' => __('models/reports.receipt.company'), 'data' => 'transactionable.quotation.company.name']),
            'payment_type' => new Column(['title' => __('models/reports.receipt.payment_type'), 'data' => 'transaction_payment_type.name','searchable' => true]),
            'clearance_date' => new Column(['title' => __('models/reports.receipt.clearance_date'), 'data' => 'clearance_date','searchable' => false]),
            'payment_no' => new Column(['title' => __('models/reports.receipt.payment_no'), 'data' => 'payment_no','searchable' => true]),
            'vat_no' => new Column(['title' => __('models/reports.receipt.vat_trn'), 'data' => 'transactionable.quotation.company.vat_no']),
            'account_type' => new Column(['title' => __('models/reports.receipt.account_type'), 'data' => 'account.type']),
            'net' => new Column(['title' => __('models/reports.receipt.net'), 'data' => 'setNetAmount', 'searchable' => false]),
            'vat' => new Column(['title' => __('models/reports.receipt.vat'), 'data' => 'transactionable.vat', 'searchable' => false]),
            'government_fee' => new Column(['title' => __('models/lpoins.fields.government_fee'), 'data' => 'setGorvementFee']),
            'total_amount' => new Column(['title' => __('models/reports.receipt.total_amount'), 'data' => 'transactionable.total_amount', 'searchable' => true]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'receipt_report_' . time();
    }
}
