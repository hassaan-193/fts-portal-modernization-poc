<?php

namespace App\DataTables;

use App\Models\Tasks;
use App\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class TaskDataTable extends DataTable
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
        // if (in_array( auth()->user()->email, Tasks::$allowedEmails)) {
        $dataTable->addColumn('action', 'tasks.datatables_actions');
        // }
        return $dataTable
        ->addColumn('status', function($query) {
            return ($query->status === 2) ? 'Close' : 'Open';
        })
        ->addColumn('assigned', function($query) {
            return (User::find($query->assigned)) ? User::find($query->assigned)->name : '';
        })
        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Tasks $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Tasks $model)
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
        $dTable = $this->builder()
        ->columns($this->getColumns())
        ->minifiedAjax();
        // if (in_array( auth()->user()->email, Tasks::$allowedEmails)) {
            $dTable->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')]);
        // }
        return $dTable->parameters([
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
            'id' => new Column(['title' => __('models/tasks.fields.id'), 'data' => 'id']),
            'start_date' => new Column(['title' =>  __('models/tasks.fields.date'), 'data' => 'start_date']),
            'title' => new Column(['title' =>  __('models/tasks.fields.name'), 'data' => 'title','searchable' => true]),
            'assigned' => new Column(['title' =>  __('models/tasks.fields.assigned'), 'data' => 'assigned','searchable' => true]),
            'description' => new Column(['title' =>  __('models/tasks.fields.description'), 'data' => 'description','searchable' => true]),
            'end_date' => new Column(['title' =>  __('models/tasks.fields.end_date'), 'data' => 'end_date']),
            'status' => new Column(['title' =>  __('models/tasks.fields.status'), 'data' => 'status']),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'stafprofile_' . time();
    }
}
