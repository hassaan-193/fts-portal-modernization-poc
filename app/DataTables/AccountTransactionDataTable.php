<?php

namespace App\DataTables;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Transaction;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class AccountTransactionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return $dataTable = new CollectionDataTable($query);
        // return $dataTable->addColumn('action', 'accounts.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Account $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $account = $this->account;
        $transactions = $model->newQuery()->whereAccountId($account->id)->whereNotNull('reference_type');
        if(request('from_date') && request('to_date')){
            $transactions->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        }

        $transactions = $transactions->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($transaction) {
            $data = json_decode(json_encode($transaction->data));
            $debit = $transaction->amount > 0 ? $transaction->amount : '';
            $credit = $transaction->amount < 0 ? abs($transaction->amount) : '';

            return [
                'date' => $transaction->created_at->toDateTimeString(),
                'transaction_detail' => $data->transaction_detail ?? '',
                'type' => class_basename($transaction->reference_type) ?? '',
                'debit' => $debit,
                'credit' => $credit,
            ];
        });

        return $transactions;
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
                'data'=> 'function(d){
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-4"B> <"col-md-4"<"table-filter">> <"col-md-4"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort'     => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                        'extend'    => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-download"></i> ' . __('auth.app.export')
                    ],
                    [
                        'extend'    => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload')
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
                "fnDrawCallback" => "function(row, data, start, end, display) {
                    var api = this.api();
    
                    // Helper to parse float
                    var intVal = function (i) {
                        return typeof i === 'string'
                            ? i.replace(/[\$,]/g, '') * 1
                            : typeof i === 'number'
                                ? i
                                : 0;
                    };
    
                    // Total Debit
                    var total_debit = api
                        .column(3)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
    
                    // Total Credit
                    var total_credit = api
                        .column(4)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
    
                    // Update footer
                    $(api.column(2).footer()).html('Total:');
                    $(api.column(3).footer()).html(total_debit.toFixed(2));
                    $(api.column(4).footer()).html(total_credit.toFixed(2));
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
            'date' => new Column(['title' => __('models/transactions.fields.date'), 'data' => 'date']),
            'transaction_detail' => new Column(['title' => __('models/transactions.fields.transaction_detail'), 'data' => 'transaction_detail']),
            'type' => new Column(['title' => __('models/transactions.fields.type'), 'data' => 'type']),
            'debit' => new Column(['title' => 'Debit', 'data' => 'debit']),
            'credit' => new Column(['title' => 'Credit', 'data' => 'credit']),
        ];
    }
    

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'accounts_' . time();
    }
}
