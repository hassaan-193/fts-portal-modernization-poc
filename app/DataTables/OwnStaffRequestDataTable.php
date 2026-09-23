<?php

namespace App\DataTables;

use App\Models\StaffRequest;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class OwnStaffRequestDataTable extends DataTable
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
        return $dataTable->addColumn('set_status', function($query) {
            $statusMap = [
                1 => ['msg' => 'Approved', 'type' => 'success'],
                2 => ['msg' => 'Rejected', 'type' => 'danger'],
                0 => ['msg' => 'Under Review', 'type' => 'warning'],
            ];

            // Fallback in case the status value is not 0, 1, or 2
            $status = $statusMap[$query->status] ?? ['msg' => 'Unknown', 'type' => 'secondary'];

            return view('components.datatables_status', [
                'msg' => $status['msg'],
                'type' => $status['type'],
            ]);
        });
    }

    /**
     * Get query source of dataTable, scoped to the authenticated user's own staff profile.
     *
     * @param \App\Models\StaffRequest $model
     * @return \Illuminate\Database\Collection\Builder
     */
    public function query(StaffRequest $model)
    {
        $model = $model->newQuery()->with('staf')->where('staf_id', auth()->user()->staf_profile_id);

        if( (request('from_date') != '' && request('to_date') != '') && !request()->get('search')['value'] ){
            $model->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        }
        if(request('status') !=null ){
            $model->where('status','=',request('status'));
        }
        return $model->orderBy('id','desc')->get();
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
                'url'=> url('/own-staff-request'),
                'data'=> 'function(d){
                    d.status= $(\'input[name=status]\').val();
                    d.from_date= $(\'input[name=from_date]\').val();
                    d.to_date= $(\'input[name=to_date]\').val();
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort' => false,
                'order'     => [[0, 'asc']],
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
                    ]
                ]
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
            'id' => new Column(['title' => __('models/labor_requests.fields.id'), 'data' => 'id','searchable' => false]),
            'created_at' => new Column(['title' => __('models/labor_requests.fields.created_at'), 'data' => 'created_at','searchable' => false]),
            'type' => new Column(['title' => __('models/labor_requests.fields.type'), 'data' => 'type','searchable' => true]),
            'start_date' => new Column(['title' => __('models/labor_requests.fields.start_date'), 'data' => 'start_date','searchable' => false]),
            'end_date' => new Column(['title' => __('models/labor_requests.fields.end_date'), 'data' => 'end_date', 'searchable' => false]),
            'advance_money' => new Column(['title' => __('models/labor_requests.fields.advance_money'), 'data' => 'advance_money','searchable' => false]),
            'notes' => new Column(['title' => __('models/labor_requests.fields.note'), 'data' => 'note', 'searchable' => false]),
            'status' => new Column(['title' => __('models/labor_requests.fields.status'), 'data' => 'set_status','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'my_requests_' . time();
    }
}
