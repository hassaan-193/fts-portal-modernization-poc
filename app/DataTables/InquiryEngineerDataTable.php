<?php

namespace App\DataTables;

use App\Models\Inquiry;
use App\Repositories\InquiryRepository;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InquiryEngineerDataTable extends DataTable
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
                return view('inquiries.engineer_actions', compact('inquiry'))->render();
            })
            ->addColumn('status_badge', function (Inquiry $inquiry) {
                $class = $inquiry->statusBadgeClass();
                return '<span class="badge badge-' . $class . '">' . e($inquiry->status) . '</span>';
            })
            ->addColumn('visit_date', function (Inquiry $inquiry) {
                $review = $inquiry->departmentReview;
                if ($review && $review->proposed_visit_date) {
                    return $review->proposed_visit_date->format('d M Y');
                }
                return '—';
            })
            ->rawColumns(['action', 'status_badge', 'visit_date']);
    }

    /**
     * Get query source scoped to engineer's site visits.
     */
    public function query(Inquiry $model)
    {
        return $this->inquiryRepository
            ->scopedQuery(auth()->user())
            ->with(['creator', 'departmentReview'])
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
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
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
     * Get columns optimized for engineers.
     */
    protected function getColumns()
    {
        return [
            new Column(['title' => 'ID',           'data' => 'id']),
            new Column(['title' => 'Inquiry No',   'data' => 'inquiry_no',       'searchable' => true]),
            new Column(['title' => 'Client',       'data' => 'client_name',      'searchable' => true]),
            new Column(['title' => 'Department',   'data' => 'assigned_department', 'searchable' => true]),
            new Column(['title' => 'Status',       'data' => 'status_badge',     'searchable' => false]),
            new Column(['title' => 'Visit Date',   'data' => 'visit_date',       'searchable' => false]),
        ];
    }

    protected function filename()
    {
        return 'engineer_site_visits_' . time();
    }
}
