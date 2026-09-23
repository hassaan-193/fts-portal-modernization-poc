<?php

namespace App\DataTables;

use App\Models\StafProfile;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class StafProfileDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'staf_profiles.datatables_actions')
        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\StafProfileDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StafProfile $model)
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
            'id' => new Column(['title' => __('models/stafprofile.fields.id'), 'data' => 'id']),
            'staf_type' => new Column(['title' =>  __('models/stafprofile.fields.staf_type'), 'data' => 'staf_type','searchable' => true]),
            'name' => new Column(['title' =>  __('models/stafprofile.fields.name'), 'data' => 'name']),
            'last_name' => new Column(['title' =>  __('models/stafprofile.fields.last_name'), 'data' => 'last_name']),
            'nationality' => new Column(['title' =>  __('models/stafprofile.fields.nationality'), 'data' => 'nationality']),
            'gender' => new Column(['title' =>  __('models/stafprofile.fields.gender'), 'data' => 'gender']),
            'joining_date' => new Column(['title' =>  __('models/stafprofile.fields.joining_date'), 'data' => 'joining_date']),
            'dob' => new Column(['title' =>  __('models/stafprofile.fields.dob'), 'data' => 'dob','searchable' => false]),
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
