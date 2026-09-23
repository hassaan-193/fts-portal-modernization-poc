<?php

namespace App\DataTables;

use App\Models\VisitScheduleHistory;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class VisitScheduleHistoryDataTable extends DataTable
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

        return $dataTable
        ->addColumn('contract_value', function ($row) {
            $value = $row->project->project_estimation ?? null;

            return $value !== null
                ? number_format($value, 2)
                : '-';
        })


            ->editColumn('created_at', function ($row) {
                return $row->created_at->toDateTimeString();
            })
            ->rawColumns([]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\VisitScheduleHistory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(VisitScheduleHistory $model)
    {
        return $model->newQuery()
            ->with('project')
            ->orderBy('created_at', 'desc');
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
            ->parameters([
                'dom'       => 'Bfrtip',
                'scrollX'   => true, 
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    'export',
                    'reload',
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
            'id'             => ['title' => 'ID'],
            'company_name'   => ['title' => 'Company Name'],
            'project_name'   => ['title' => 'Project Name'],
            'contract_value' => ['title' => 'Contract Values'],
            'created_at'     => ['title' => 'Expired At'],
        ];
    }


    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'VisitScheduleHistory_' . date('YmdHis');
    }
}
