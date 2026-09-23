<?php

namespace App\DataTables;

use App\Models\RequestForm;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class RequestFormDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'request_forms.datatables_actions')
        ->addColumn('request_status', function($query) {
            return view('components.datatables_status', [
                'type' => __('messages.status.'.$query->status),
                'msg' => config('enum.request_form_status.' .$query->status),
            ]);
        })
        ->rawColumns(['action','model']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\RequestForm $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RequestForm $model)
    {
        return $model->newQuery()->whereUserId(\Auth::id())->orderBy('id','desc');;
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
            'date_time' => new Column(['title' => __('models/request_forms.fields.date_time'), 'data' => 'date_time','searchable' => false]),
            'name' => new Column(['title' => __('models/request_forms.fields.name'), 'data' => 'name','searchable' => true]),
            'status' => new Column(['title' => __('models/request_forms.fields.status'), 'data' => 'request_status','searchable' => false]),
            'note' => new Column(['title' => __('models/request_forms.fields.note'), 'data' => 'note','searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'request_forms_' . time();
    }
}
