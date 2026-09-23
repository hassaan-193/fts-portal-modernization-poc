<?php

namespace App\DataTables;

use App\Models\PettyCash;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class PettyCashRegisterDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return $dataTable = new EloquentDataTable($query);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Account $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PettyCash $model)
    {
        $model =  $model->newQuery()->with(['account','vendor'])->has('vendor')->orderBy('id','desc');;
        if(request('from_date') != '' && request('to_date') != ''){
            $model->whereDate('date_time', '>=', request('from_date'))
                ->whereDate('date_time', '<=', request('to_date'));
        }
        return $model;
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
                'url'=> url('reports/pettycash/register') ,
                'data'=> 'function(d){
                    d.name= $(\'input[name=name]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-2"B> <"col-md-10"<"table-filter">> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
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
                        .column( 6 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    vat = api
                        .column( 7 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    total_amount = api
                        .column( 8 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 5 ).footer() ).html('Total:' );
                    $( api.column( 7 ).footer() ).html(net.toFixed(2));
                    $( api.column( 6 ).footer() ).html(vat.toFixed(2));
                    $( api.column( 8 ).footer() ).html(total_amount.toFixed(2));
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
            'date' => new Column(['title' => __('models/reports.pettycash.register.date'), 'data' => 'date_time']),
            'vendor' => new Column(['title' => __('models/reports.pettycash.register.vendor'), 'data' => 'vendor.name']),
            'vat_no' => new Column(['title' => __('models/reports.pettycash.register.vat_trn'), 'data' => 'vendor.vat_no']),
            'voucher' => new Column(['title' => __('models/reports.pettycash.register.voucher'), 'data' => 'voucher_no']),
            'account_type' => new Column(['title' => __('models/reports.pettycash.register.account_type'), 'data' => 'account.type']),
            'notes' => new Column(['title' => __('models/reports.pettycash.register.notes'), 'data' => 'description']),
            'net' => new Column(['title' => __('models/reports.pettycash.register.net'), 'data' => 'amount']),
            'vat' => new Column(['title' => __('models/reports.pettycash.register.vat'), 'data' => 'vat']),
            'total_amount' => new Column(['title' => __('models/reports.pettycash.register.total_amount'), 'data' => 'total_amount']),
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
