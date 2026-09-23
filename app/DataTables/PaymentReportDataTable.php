<?php

namespace App\DataTables;

use App\Models\Payment;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Services\DataTable;

class PaymentReportDataTable extends DataTable
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
        return $dataTable->addColumn("set_vendor", function ($query){
            $inv = $query->transactionable;
            if (!$inv) return '';
            // LPO-linked payable
            if ($inv->lpoout && $inv->lpoout->vendor) return $inv->lpoout->vendor->name;
            // Direct vendor_id (vendor payable / no LPO)
            if ($inv->vendor) return $inv->vendor->name;
            // Text fallback (unmatched import)
            return $inv->historical_party ?? '';
        })
        ->addColumn("set_vendor_vat_no", function ($query){
            $inv = $query->transactionable;
            if (!$inv) return '';
            if ($inv->lpoout && $inv->lpoout->vendor) return $inv->lpoout->vendor->vat_no;
            if ($inv->vendor) return $inv->vendor->vat_no ?? '';
            return '';
        })
        ->addColumn("set_lpoout", function ($query){
            return $query->transactionable && $query->transactionable->lpoout ? $query->transactionable->lpoout->name : "";
        })
        ->addColumn("set_invoice", function ($query){
            return $query->transactionable ? $query->transactionable->invoice_no : "";
        })
        ->addColumn("set_note", function ($query){
            return $query->transactionable ? $query->transactionable->note : "";
        })
        ->addColumn("set_amount", function ($query){
            return $query->transactionable ? $query->transactionable->amount : "";
        })
        ->addColumn("set_vat", function ($query){
            return $query->transactionable ? $query->transactionable->vat : "";
        })
        ->addColumn("set_total_amount", function ($query){
            return $query->transactionable ? $query->transactionable->total_amount : "";
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Account $model
     * @return \Illuminate\Database\Collection\Builder
     */
    public function query(Payment $model)
    {
        $model = $model->newQuery()->with([
            'account:id,type',
            'transactionable:id,invoice_no,lpoout_id,vendor_id,note,vat,amount,total_amount,historical_party',
            'transactionable.lpoout:id,name,vendor_id',
            'transactionable.lpoout.vendor:id,name,vat_no',
            'transactionable.vendor:id,name,vat_no',
            'transaction_payment_type',
            ])
            ->select('id','date_time','transactionable_id','transactionable_type','account_id','transaction_type','payment_type','clearance_date','payment_no')
            ->where('type','Payment')
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
                'url'=> url('reports/payment') ,
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
                    total_amount = api
                        .column( 13 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 10 ).footer() ).html('Total:' );
                    $( api.column( 11 ).footer() ).html(net.toFixed(2));
                    $( api.column( 12 ).footer() ).html(vat.toFixed(2));
                    $( api.column( 13 ).footer() ).html(total_amount.toFixed(2));
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
            'id' => new Column(['title' => __('models/reports.payment.id'), 'data' => 'id','searchable' => false]),
            'date' => new Column(['title' => __('models/reports.payment.date'), 'data' => 'date_time', 'searchable' => false]),
            'vendor' => new Column(['title' => __('models/reports.payment.vendor'), 'data' => 'set_vendor']),
            'vat_no' => new Column(['title' => __('models/reports.payment.vat_trn'), 'data' => 'set_vendor_vat_no']),
            'voucher' => new Column(['title' => __('models/reports.payment.voucher'), 'data' => 'set_invoice']),
            'lpo_out' => new Column(['title' => __('models/reports.payment.lpo_out'), 'data' => 'set_lpoout']),
            'payment_type' => new Column(['title' => __('models/reports.payment.payment_type'), 'data' => 'transaction_payment_type.name','searchable' => true]),
            'clearance_date' => new Column(['title' => __('models/reports.payment.clearance_date'), 'data' => 'clearance_date','searchable' => false]),
            'payment_no' => new Column(['title' => __('models/reports.payment.payment_no'), 'data' => 'payment_no','searchable' => false]),
            'account_type' => new Column(['title' => __('models/reports.payment.account_type'), 'data' => 'account.type']),
            'notes' => new Column(['title' => __('models/reports.payment.notes'), 'data' => 'set_note', 'searchable' => false]),
            'net' => new Column(['title' => __('models/reports.payment.net'), 'data' => 'set_amount', 'searchable' => false]),
            'vat' => new Column(['title' => __('models/reports.payment.vat'), 'data' => 'set_vat', 'searchable' => false]),
            'total_amount' => new Column(['title' => __('models/reports.payment.total_amount'), 'data' => 'set_total_amount', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'petty_cash_register_' . time();
    }
}
