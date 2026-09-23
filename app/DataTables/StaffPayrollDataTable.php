<?php

namespace App\DataTables;

use App\Models\StaffPayroll;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class StaffPayrollDataTable extends DataTable
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
        return $dataTable->addColumn('action', 'staff_payrolls.datatables_actions')
        ->addColumn('set_date', function($query) {
            return $query->date->format('Y-m-d');
        })
        ->addColumn('set_name', function($query) {
            return $query->profile->name;
        })
        ->addColumn('set_total_salary', function($query) {
            return $query->profile->total_salary;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\StaffPayroll $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StaffPayroll $model)
    {
        $model = $model->newQuery()->with('profile')->orderBy('id', 'desc');

        if (request('date')) {
            // If a date is provided, filter using the given date
            $model->whereDate('date', '>=', request('date') . '-01')
                ->whereDate('date', '<=', request('date') . '-01');
        } else {
            // If no date is provided, filter for the current month
            $currentMonthStart = now()->startOfMonth()->toDateString();
            $currentMonthEnd = now()->endOfMonth()->toDateString();

            $model->whereDate('date', '>=', $currentMonthStart)
                ->whereDate('date', '<=', $currentMonthEnd);
        }

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
                'url'=> url('/staffPayrolls') ,
                'data'=> 'function(d){
                    d.date= $(\'input[name=date]\').val();
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
            'date' => new Column(['title' => __('models/staff_payrolls.fields.date'), 'data' => 'set_date','searchable' => false]),
            'name' => new Column(['title' => __('models/staff_payrolls.fields.name'), 'data' => 'set_name','searchable' => true]),
            'total_salary' => new Column(['title' => __('models/staff_payrolls.fields.total_salary'), 'data' => 'set_total_salary','searchable' => false]),
            'hours' => new Column(['title' => __('models/staff_payrolls.fields.hours'), 'data' => 'hours','searchable' => false]),
            'plus_adjustment' => new Column(['title' => __('models/staff_payrolls.fields.plus_adjustment'), 'data' => 'plus_adjustment','searchable' => false]),
            'minus_adjustment' => new Column(['title' => __('models/staff_payrolls.fields.minus_adjustment'), 'data' => 'minus_adjustment','searchable' => false]),
            'total_amount' => new Column(['title' => __('models/staff_payrolls.fields.total_amount'), 'data' => 'total_amount','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'staff_payrolls_' . time();
    }
}
