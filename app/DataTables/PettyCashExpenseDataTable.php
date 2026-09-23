<?php

namespace App\DataTables;

use App\Models\PettyCashExpense;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PettyCashExpenseDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'petty_cash_expenses.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PettyCashExpense $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PettyCashExpense $model)
    {
        return $model->newQuery()->orderBy('id','desc');;
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
            'id' => new Column(['title' => __('models/petty_cash_expenses.fields.id'), 'data' => 'id','searchable' => false]),
            'name' => new Column(['title' => __('models/petty_cash_expenses.fields.name'), 'data' => 'name']),
            'category' => new Column(['title' => __('models/petty_cash_expenses.fields.category'), 'data' => 'category']),
            'amount' => new Column(['title' => __('models/petty_cash_expenses.fields.amount'), 'data' => 'amount']),
            'date' => new Column(['title' => __('models/petty_cash_expenses.fields.date'), 'data' => 'date']),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'petty_cash_expenses' . time();
    }
}
