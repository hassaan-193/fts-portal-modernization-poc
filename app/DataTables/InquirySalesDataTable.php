<?php

namespace App\DataTables;

use App\Models\Inquiry;
use App\Repositories\InquiryRepository;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class InquirySalesDataTable extends DataTable
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
            ->editColumn('follow_up_date', function (Inquiry $inquiry) {
                return $inquiry->follow_up_date
                    ? \Carbon\Carbon::parse($inquiry->follow_up_date)->format('d M Y')
                    : '';
            })

            ->editColumn('created_at', function (Inquiry $inquiry) {
                return $inquiry->created_at
                    ? $inquiry->created_at->format('d M Y h:i A')
                    : '';
            })

            ->addColumn('action', function (Inquiry $inquiry) {
                return view('inquiries.sales_actions', compact('inquiry'))->render();
            })

            ->addColumn('status_badge', function (Inquiry $inquiry) {
                $class = $inquiry->statusBadgeClass();

                return '<span class="badge badge-' . $class . '">' . e($inquiry->status) . '</span>';
            })

            ->addColumn('quote_status', function (Inquiry $inquiry) {
                if ($inquiry->quotation) {
                    return '<span class="badge badge-success">Created</span>';
                }

                return '<span class="badge badge-warning">Pending</span>';
            })

            ->rawColumns(['action', 'status_badge', 'quote_status']);
    }

    /**
     * Get query source scoped to sales' inquiries.
     */
    public function query(Inquiry $model)
    {
        return $this->inquiryRepository
            ->scopedQuery(auth()->user())
            ->with(['creator', 'quotation', 'followUps'])
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
     * Get columns optimized for sales users.
     */
    protected function getColumns()
    {
        return [
            new Column(['title' => 'ID',              'data' => 'id']),
            new Column(['title' => 'Inquiry No',      'data' => 'inquiry_no',        'searchable' => true]),
            new Column(['title' => 'Client',          'data' => 'client_name',       'searchable' => true]),
            new Column(['title' => 'Status',          'data' => 'status_badge',      'searchable' => false]),
            new Column(['title' => 'Quote Status',    'data' => 'quote_status',      'searchable' => false]),
            new Column(['title' => 'Follow-up Date',  'data' => 'follow_up_date',    'searchable' => false]),
            new Column(['title' => 'Created At',      'data' => 'created_at',        'searchable' => false]),
        ];
    }

    protected function filename()
    {
        return 'sales_pipeline_' . time();
    }
}
