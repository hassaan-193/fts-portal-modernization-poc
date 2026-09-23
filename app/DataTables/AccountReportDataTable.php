<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Receipt;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class AccountReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatable =  new CollectionDataTable($query);
        return $datatable->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Receipt $model
     * @return \Illuminate\Database\Collection\Builder
     */
    public function query(Receipt $model)
    {
        $model = $model->newQuery()->with('account','transaction_payment_type')->whereNull('transactionable_id');

        if(request('name')){
            $model->whereHas('account',function($q){
                $q->where('id', request('name'));
            });
        }

        if(request('from_date') && request('to_date'))
            $model->whereDate('date_time', '>=', request('from_date'))
                ->whereDate('date_time', '<=', request('to_date'));

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
                'url'=> url('reports/account') ,
                'data'=> 'function(d){
                    d.name= $(\'select[name=name]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-2"B> <"col-md-8"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
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
                    var table= $('#dataTableBuilder').DataTable();
                    for (let index = 0; index < table.data().count(); index++) {
                        if (table.cell(index,5).data()=='Payment'){
                            table.cell(index,3).data(-(table.cell(index,3).data()));
                        }
                    }

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    total = api
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 2 ).footer() ).html('Total:' );
                    $( api.column( 3 ).footer() ).html(total.toFixed(2));
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
            'id' => new Column(['title' => "Sr #", 'data' => 'DT_RowIndex','searchable' => false]),
            'date' => new Column(['title' => __('models/reports.account.date'), 'data' => 'date_time','searchable' => false]),
            'name' => new Column(['title' => __('models/reports.account.name'), 'data' => 'account.type','searchable' => true]),
            'total_amount' => new Column(['title' => __('models/reports.account.total_amount'), 'data' => 'total','searchable' => true]),
            'notes' => new Column(['title' => __('models/reports.account.notes'), 'data' => 'note','searchable' => true]),
            'type' => new Column(['title' =>"type", 'data' => 'type','searchable' => false, 'visible' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'account_' . time();
    }

}
