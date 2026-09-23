<?php

namespace App\DataTables;

use App\Models\DrawingReceived;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class DrawingReceivedDataTable extends DataTable
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

    return $dataTable->addColumn('action', 'drawing-receiveds.datatables_actions')
        ->addColumn('engineer', function($query) {
            return $query->responsibleEngineer->name ?? 'N/A';
        })
        ->addColumn('lpoin', function($query) {
            return view('components.datatables_relation_link', [
                'id' => $query->lpoin_id,
                'name' => $query->lpoin->ref_no ?? 'N/A',
                'model' => 'lpoins'
            ]);
        })
        ->addColumn('start_date', function($query) {
            return $query->start_date ? $query->start_date->format('Y-m-d') : 'N/A';
        })
        ->addColumn('approval_date', function($query) {
            return $query->approval_date ? $query->approval_date->format('Y-m-d') : 'N/A';
        })
        ->addColumn('contributions_count', function($query) {
            return '<span class="badge badge-secondary">' . $query->contributions->count() . '</span>';
        })
        ->rawColumns(['action', 'lpoin', 'contributions_count']);
}

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DrawingReceived $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DrawingReceived $model)
    {
        return $model->newQuery()
            ->with('lpoin', 'responsibleEngineer', 'contributions')
            ->orderBy('id', 'desc')
            ->get();
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
            ->ajax(['url' => route('drawing-receiveds.index'), 'type' => 'GET'])
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'scrollX'   => true,
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'bSort'     => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                        'extend' => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-download"></i> ' . __('auth.app.export') . ''
                    ],
                    [
                        'extend' => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload') . ''
                    ],
                    [
                        'extend' => 'create',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-plus"></i> ' . __('auth.app.create') . ''
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
            'id' => new Column(['title' => __('models/drawing_receiveds.fields.id'), 'data' => 'id']),
            'lpoin' => new Column(['title' => __('models/drawing_receiveds.fields.lpoin_id'), 'data' => 'lpoin', 'name' => 'lpoin.ref_no']),
            'type_of_work' => new Column(['title' => __('models/drawing_receiveds.fields.type_of_work'), 'data' => 'type_of_work']),
            'engineer' => new Column(['title' => __('models/drawing_receiveds.fields.responsible_engineer_id'), 'data' => 'engineer', 'name' => 'responsibleEngineer.name']),
            'start_date' => new Column(['title' => __('models/drawing_receiveds.fields.start_date'), 'data' => 'start_date', 'searchable' => false]),
            'approval_date' => new Column(['title' => __('models/drawing_receiveds.fields.approval_date'), 'data' => 'approval_date', 'searchable' => false]),
            'contributions_count' => new Column(['title' => 'Contributions', 'data' => 'contributions_count', 'searchable' => false]),
        ];
    }
}
