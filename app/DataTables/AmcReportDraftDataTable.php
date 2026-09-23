<?php

namespace App\DataTables;

use App\Models\ProjectReport;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

/**
 * AMC reports the signed-in user has saved as a draft and not yet submitted.
 *
 * Drafts are private to whoever started them; once submitted they drop off this
 * list and appear on the report-status page for approval instead.
 */
class AmcReportDraftDataTable extends DataTable
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
            ->addColumn('action', 'projects.drafts_datatables_actions')
            ->addColumn('client', function ($report) {
                return $report->company->name ?? $report->manual_client_name ?? 'N/A';
            })
            ->addColumn('project_name', function ($report) {
                return $report->project->subject ?? 'N/A';
            })
            ->editColumn('amc_type', function ($report) {
                return ucfirst($report->amc_type);
            })
            ->editColumn('updated_at', function ($report) {
                return $report->updated_at ? $report->updated_at->format('d M Y h:i A') : '—';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ProjectReport $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ProjectReport $model)
    {
        // Only the listed columns are selected: block_info and client_signature can be
        // very large and would otherwise be serialized into every AJAX response.
        return $model->newQuery()
            ->select([
                'id', 'reference_number', 'date', 'amc_type', 'company_id',
                'manual_client_name', 'project_id', 'site_name', 'updated_at',
            ])
            ->with(['company:id,name', 'project:id,subject'])
            ->where('status', ProjectReport::STATUS_DRAFT)
            ->where('created_by', auth()->id());
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
                'order'     => [[6, 'desc']],
                'buttons'   => [
                    [
                        'extend'    => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload') . '',
                    ],
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
            'reference_number' => new Column(['title' => 'Reference No', 'data' => 'reference_number', 'searchable' => true]),
            'date'             => new Column(['title' => 'Report Date', 'data' => 'date', 'searchable' => false]),
            'amc_type'         => new Column(['title' => 'AMC Type', 'data' => 'amc_type', 'searchable' => false]),
            'client'           => new Column(['title' => 'Client', 'data' => 'client', 'searchable' => false, 'orderable' => false]),
            'project'          => new Column(['title' => 'Project', 'data' => 'project_name', 'searchable' => false, 'orderable' => false]),
            'site_name'        => new Column(['title' => 'Site Name', 'data' => 'site_name', 'searchable' => true]),
            'updated_at'       => new Column(['title' => 'Last Saved', 'data' => 'updated_at', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'amc_report_drafts_' . time();
    }
}
