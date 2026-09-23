<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'users.datatables_actions')
        ->addColumn('user_role', 'users.user_roles')
        ->rawColumns(['action','user_role']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->with('roles')->orderBy('id' , 'DESC');
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
                    'dom'       => '<"row" <"col-md-4"B> <"col-md-4"<"table-filter">> <"col-md-4"f> >rt<"row" <"col-md-6"i> <"col-md-6"p> >',
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
            'name',
            'email',
            [
                'title' => 'Roles',
                'data' => 'user_role'
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'users_' . time();
    }
}
