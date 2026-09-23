<?php

namespace App\DataTables;

use App\Models\Transaction;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class TrialBalanceReportDataTable extends DataTable
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

        return $dataTable->addColumn('net_debit', function ($row) {
            return $row->net_debit > 0 ? number_format($row->net_debit, 2) : '0.00';
        })
        ->addColumn('net_credit', function ($row) {
            return $row->net_credit > 0 ? number_format($row->net_credit, 2) : '0.00';
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transaction $model
     * @return \Illuminate\Support\Collection
     */
    public function query(Transaction $model)
    {
        $model = $model->newQuery()
            ->select(
                'balance_accounts.type as account_name',
                DB::raw("
                    CASE 
                        WHEN SUM(balance_transactions.amount) >= 0 
                            THEN SUM(balance_transactions.amount) 
                        ELSE 0 
                    END as net_debit
                "),
                DB::raw("
                    CASE 
                        WHEN SUM(balance_transactions.amount) < 0 
                            THEN ABS(SUM(balance_transactions.amount)) 
                        ELSE 0 
                    END as net_credit
                ")
            )
            ->join('balance_accounts', 'balance_transactions.account_id', '=', 'balance_accounts.id')
            ->groupBy('balance_transactions.account_id');

        if (request('from_date') && request('to_date')) {
            $model->whereDate('balance_transactions.created_at', '>=', request('from_date'))
                ->whereDate('balance_transactions.created_at', '<=', request('to_date'));
        }

        // 🔑 Only return rows where net_debit or net_credit is not zero
        $model->havingRaw('SUM(balance_transactions.amount) != 0');

        return $model->get();
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
                'url'=> url('reports/trial-balance') ,
                'data'=> 'function(d){
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort'     => false,
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
                "footerCallback" => "function(row, data, start, end, display) {
                    var api = this.api();

                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ? i : 0;
                    };

                    // Total debit
                    var debitTotal = api
                        .column(1)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total credit
                    var creditTotal = api
                        .column(2)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    $(api.column(0).footer()).html('Total');
                    $(api.column(1).footer()).html(debitTotal.toFixed(2));
                    $(api.column(2).footer()).html(creditTotal.toFixed(2));
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
            'account_name' => new Column(['title' => __('models/reports.trial_balance.name'), 'data' => 'account_name']),
            'net_debit'    => new Column(['title' => __('models/reports.trial_balance.net_debit'), 'data' => 'net_debit', 'searchable' => false]),
            'net_credit'   => new Column(['title' => __('models/reports.trial_balance.net_credit'), 'data' => 'net_credit', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'trial_balance_' . time();
    }
}
