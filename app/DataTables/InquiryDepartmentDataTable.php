<?php

namespace App\DataTables;

use App\Models\Inquiry;
use App\Repositories\InquiryRepository;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InquiryDepartmentDataTable extends DataTable
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
            ->addColumn('action', function (Inquiry $inquiry) {
                return view('inquiries.department_actions', compact('inquiry'))->render();
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
            ->addColumn('assigned_to', function (Inquiry $inquiry) {
                $review = $inquiry->departmentReview;
                if ($review && $review->assignedUser) {
                    return $review->assignedUser->name;
                }
                return '—';
            })
            ->rawColumns(['action', 'status_badge', 'priority_badge', 'assigned_to']);
    }

    /**
     * Get query source scoped to department user's inquiries.
     */
    public function query(Inquiry $model)
    {
        return $this->inquiryRepository
            ->scopedQuery(auth()->user())
            ->with(['creator', 'departmentReview.assignedUser'])
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
                        'text'      => '<i class="fa fa-download"></i> Export',
                    ],
                    [
                        'extend'    => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-refresh"></i> Reload',
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    /**
     * Get columns optimized for department users.
     */
    protected function getColumns()
    {
        return [
            new Column(['title' => 'ID',           'data' => 'id']),
            new Column(['title' => 'Inquiry No',   'data' => 'inquiry_no',       'searchable' => true]),
            new Column(['title' => 'Client',       'data' => 'client_name',      'searchable' => true]),
            new Column(['title' => 'Type',         'data' => 'inquiry_type',     'searchable' => true]),
            new Column(['title' => 'Status',       'data' => 'status_badge',     'searchable' => false]),
            new Column(['title' => 'Priority',     'data' => 'priority_badge',   'searchable' => false]),
            new Column(['title' => 'Assigned To',  'data' => 'assigned_to',      'searchable' => false]),
        ];
    }

    protected function filename()
    {
        return 'department_inquiries_' . time();
    }
}
