<?php

namespace App\DataTables;

use App\Models\Project;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ProjectDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'projects.datatables_actions')
        ->addColumn('quotation_link', function($query) {
            if (!$query->quotation) {
                return '-';
            }
            return view('components.datatables_relation_link', [
                'id' => $query->quotation->id,
                'name' => $query->quotation->name,
                'model' => 'quotations'
            ]);
        })
        ->addColumn('company_link', function($query) {
            if (!$query->quotation || !$query->quotation->company) {
                return '-';
            }
            return view('components.datatables_relation_link', [
                'id' => $query->quotation->company->id,
                'name' => $query->quotation->company->name,
                'model' => 'companies'
            ]);
        })
        ->addColumn('contract_value', function($query) {
            return $query->quotation ? $query->quotation->amount : '-';
        })
        ->addColumn('set_engineer', function($query) {
            return $query->engineer->name ?? "" ;
        })
        ->rawColumns(['action','quotation_link','company_link']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Project $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Project $model)
    {
        return $model->newQuery()->with(['quotation.company','engineer:id,name'])->orderBy('id','desc');;
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
            'date' => new Column(['title' => __('models/projects.fields.date'), 'data' => 'date']),
            'quotation_id' => new Column(['title' => __('models/projects.fields.quotation_id'), 'data' => 'quotation_link', 'name' => 'quotation.name']),
            'company_id' => new Column(['title' => __('models/quotations.fields.company_id'), 'data' => 'company_link', 'name' => 'quotation.company.name']),
            'user_id' => new Column(['title' => __('models/projects.fields.user_id'), 'data' => 'set_engineer']),
            'contract_value' => new Column(['title' => __('models/projects.fields.contract_value'), 'data' => 'contract_value']),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'projects_' . time();
    }
}
