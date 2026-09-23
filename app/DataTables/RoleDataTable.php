<?php

namespace App\DataTables;


use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'roles.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->newQuery()->orderBy('id' , 'DESC');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (auth()->user()->hasRole([1])) {
            $buttons = [
                ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
                ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
                ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
            ];
        }else{
            $buttons = [
                ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
                ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
            ];
        }
            return $this->builder()
                ->columns($this->getColumns())
                ->minifiedAjax()
                ->addAction(['width' => '120px', 'printable' => false])
                ->parameters([
                    'dom'       => '<"row" <"col-md-4"B> <"col-md-6"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"i> <"col-md-6"p> >',
                    'stateSave' => true,
                    'bSort' => false,
                    'order'     => [[0, 'desc']],
                    'buttons'   => $buttons,
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
            'name'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'rolesdatatable_' . time();
    }
}
