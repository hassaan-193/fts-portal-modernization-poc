<?php

namespace App\DataTables;

use App\Models\Inquiry;
use App\Repositories\InquiryRepository;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InquiryDataTable extends DataTable
{
    /** @var InquiryRepository */
    protected $inquiryRepository;

    public function __construct(InquiryRepository $inquiryRepository)
    {
        $this->inquiryRepository = $inquiryRepository;
    }

    /**
     * Build DataTable class.
     */
    public function dataTable($query)
    {
        $dataTable = new CollectionDataTable($query);

        return $dataTable
            ->editColumn('created_at', function (Inquiry $inquiry) {
                return $inquiry->created_at
                    ? $inquiry->created_at->format('d M Y h:i A')
                    : '';
            })
            ->addColumn('action', function (Inquiry $inquiry) {
                return view('inquiries.datatables_actions', compact('inquiry'))->render();
            })
            ->addColumn('status_badge', function (Inquiry $inquiry) {
                $class = $inquiry->statusBadgeClass();
                return '<span class="badge badge-' . $class . '">' . e($inquiry->status) . '</span>';
            })
            ->addColumn('priority_badge', function (Inquiry $inquiry) {
                $map = ['Low' => 'success', 'Medium' => 'warning', 'High' => 'danger'];
                $class = $map[$inquiry->priority] ?? 'secondary';
                return '<span class="badge badge-' . $class . '">' . e($inquiry->priority) . '</span>';
            })
            ->rawColumns(['action', 'status_badge', 'priority_badge']);
    }

    /**
     * Get query source scoped to the authenticated user's role.
     */
    public function query(Inquiry $model)
    {
        return $this->inquiryRepository
            ->scopedQuery(auth()->user())
            ->with(['creator'])
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '150px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'bSort'     => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                        'extend'    => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-download"></i> ' . __('auth.app.export'),
                    ],
                    [
                        'extend'    => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload'),
                    ],
                    [
                        'extend'    => 'create',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-plus"></i> ' . __('auth.app.create'),
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    /**
     * Get columns.
     */
    protected function getColumns()
    {
        return [
            new Column(['title' => 'ID',           'data' => 'id']),
            new Column(['title' => 'Inquiry No',   'data' => 'inquiry_no',       'searchable' => true]),
            new Column(['title' => 'Client',        'data' => 'client_name',      'searchable' => true]),
            new Column(['title' => 'Project',       'data' => 'project',          'searchable' => true]),
            new Column(['title' => 'Type',          'data' => 'inquiry_type',     'searchable' => true]),
            new Column(['title' => 'Department',    'data' => 'assigned_department', 'searchable' => true]),
            new Column(['title' => 'Status',        'data' => 'status_badge',     'searchable' => false]),
            new Column(['title' => 'Priority',      'data' => 'priority_badge',   'searchable' => false]),
            new Column(['title' => 'Created By',    'data' => 'creator.name',     'searchable' => false]),
            new Column(['title' => 'Created At',    'data' => 'created_at',       'searchable' => false]),
        ];
    }

    protected function filename()
    {
        return 'inquiries_' . time();
    }
}
