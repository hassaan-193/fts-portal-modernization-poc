<?php

namespace App\DataTables;

use App\Models\DrawingReceived;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class EngineerDrawingDataTable extends DataTable
{
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'drawing-receiveds.engineer_datatables_actions')
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
            ->addColumn('contributions_count', function($query) {
                return '<span class="badge badge-secondary">' . $query->contributions->count() . '</span>';
            })
            ->rawColumns(['action', 'lpoin', 'contributions_count']);
    }

    public function query(DrawingReceived $model)
    {
        $userId = auth()->user()->id;

        return $model->newQuery()
            ->where('responsible_engineer_id', $userId)
            ->with('lpoin', 'responsibleEngineer', 'contributions')
            ->orderBy('created_at', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax([
                'url'  => route('drawing-receiveds.engineer-dashboard'),
                'type' => 'GET'
            ])
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
                        'text' => '<i class="fa fa-download"></i> ' . __('auth.app.export')
                    ],
                    [
                        'extend' => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload')
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('lpoin')->title('LPOIN Ref'),
            Column::make('type_of_work')->title('Type of Work'),
            Column::make('start_date')->title('Start Date'),
            Column::make('contributions_count')->title('Contributions'),
            
            // Action column at the last
            Column::computed('action')->title(__('crud.action'))->printable(false)->orderable(false)->searchable(false),
        ];
    }

    protected function filename()
    {
        return 'engineerDrawings_' . time();
    }
}