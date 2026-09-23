<?php

namespace App\DataTables;

use App\Models\Document;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class StaffProfileDocumentsDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'documents.datatables_actions')
        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\StafProfileDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Document $model)
    {

        $model = $model->newQuery();
        if(!request('doc_type')){
            $model;
            // $model->where('type', '!=', 'default');
        }else if(request('doc_type')){
            $model->where('type', '=', request('doc_type'));
        }
        return $model->orderBy('id','desc');

        // return $model->newQuery()->orderBy('id' , 'DESC');
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
        ->ajax([
            'url'=> url('document') ,
            'data'=> 'function(d){
                d.id= $(\'input[name=staff_id]\').val();
                d.doc_type= $(\'select[name=document_type]\').val();
            }'
        ])
        ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
        ->parameters([
            'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
            'stateSave' => true,
            'bSort' => false,
            'order'     => [[0, 'desc']],
            'buttons'   => [
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
            'type' => new Column(['title' =>  __('models/document.fields.type'), 'data' => 'type','searchable' => true]),
            'name' => new Column(['title' =>  __('models/document.fields.name'), 'data' => 'name']),
            'date' => new Column(['title' =>  __('models/document.fields.date'), 'data' => 'date']),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'document_' . time();
    }
}
