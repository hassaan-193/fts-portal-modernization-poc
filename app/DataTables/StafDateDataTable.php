<?php

namespace App\DataTables;

use App\Models\StafDates;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;



class StafDateDataTable extends DataTable
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

        return $dataTable->addColumn('staff_name', function($row) {
                return $row->name ?? 'N/A';
            })
            ->addColumn('action', 'staf_dates.datatables_actions')
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\StafDates $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StafDates $model)
    {
        $query = $model->newQuery()
            ->with('staff')
            ->join('staf_profile', 'staf_dates.staff_id', '=', 'staf_profile.id')
            ->select('staf_dates.*', 'staf_profile.name')
            ->orderBy('staf_dates.id', 'DESC');
        
        return $query;
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
                   'className' => 'btn btn-default btn-sm no-corner',
                   'text' => '<i class="fa fa-plus"></i> ' .__('auth.app.create').'',
                   'action' => 'function() { window.location.href = "' . route('staf-dates.create') . '"; }'
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
            'staff_name' => new Column(['title' => 'Staff Member', 'data' => 'staff_name', 'searchable' => true, 'name' => 'staf_profile.name']),
            'start_date' => new Column(['title' =>  __('models/stafdates.fields.start_date'), 'data' => 'start_date','searchable' => true, 'name' => 'staf_dates.start_date']),
            'end_date' => new Column(['title' =>   __('models/stafdates.fields.end_date'), 'data' => 'end_date','searchable' => true, 'name' => 'staf_dates.end_date']),
            'days' => new Column(['title' =>  __('models/stafdates.fields.days'), 'data' => 'days', 'name' => 'staf_dates.days'])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'StafDate_' . date('YmdHis');
    }
}
