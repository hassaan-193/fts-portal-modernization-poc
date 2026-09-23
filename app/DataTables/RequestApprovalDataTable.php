<?php

namespace App\DataTables;

use App\Services\RequestApprovalService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

/**
 * The approvers' queue: Staff and Labor requests merged into one table, scoped to
 * the request types the signed-in user holds an approval permission for.
 */
class RequestApprovalDataTable extends DataTable
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

        return $dataTable
            ->addColumn('action', function ($row) {
                return view('request_approvals.datatables_actions', $row)->render();
            })
            ->addColumn('side', function ($row) {
                $cls = $row['type'] === 'labor' ? 'info' : 'primary';

                return '<span class="badge badge-' . $cls . '">' . $row['type_label'] . '</span>';
            })
            ->addColumn('approvals', function ($row) {
                return RequestApprovalService::progressBadges($row['progress']);
            })
            ->addColumn('set_status', function ($row) {
                return RequestApprovalService::statusBadge($row['status']);
            })
            ->rawColumns(['action', 'side', 'approvals', 'set_status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return RequestApprovalService::feed(
            RequestApprovalService::typesForUser(),
            request('filter') === 'all' ? 'all' : 'pending'
        );
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
                'url'  => url('/request-approvals'),
                'data' => 'function(d){
                    d.filter = $(\'input[name=filter]\').val();
                }',
            ])
            ->addAction(['width' => '210px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => true,
                'bSort'     => false,
                'order'     => [[0, 'asc']],
                'buttons'   => [
                    [
                        'extend'    => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-download"></i> ' . __('auth.app.export') . '',
                    ],
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
            'id'            => new Column(['title' => 'Id', 'data' => 'id', 'searchable' => false]),
            'side'          => new Column(['title' => 'Side', 'data' => 'side', 'searchable' => false]),
            'created_at'    => new Column(['title' => 'Submitted', 'data' => 'created_at', 'searchable' => false]),
            'requester'     => new Column(['title' => 'Requester', 'data' => 'requester', 'searchable' => true]),
            'request_type'  => new Column(['title' => 'Request Type', 'data' => 'request_type', 'searchable' => true]),
            'start_date'    => new Column(['title' => 'Start Date', 'data' => 'start_date', 'searchable' => false]),
            'end_date'      => new Column(['title' => 'End Date', 'data' => 'end_date', 'searchable' => false]),
            'advance_money' => new Column(['title' => 'Advance Money', 'data' => 'advance_money', 'searchable' => false]),
            'approvals'     => new Column(['title' => 'Approvals', 'data' => 'approvals', 'searchable' => false]),
            'status'        => new Column(['title' => 'Status', 'data' => 'set_status', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'request_approvals_' . time();
    }
}
