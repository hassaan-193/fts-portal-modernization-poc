<?php

namespace App\DataTables;

use App\Models\InvoiceRequest;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class InvoiceRequestPaymentDataTable extends DataTable
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

        return $dataTable->addColumn('action', function($query){
            if(!$query->status)
                return "<div class='btn-group'>
                    <a href='" . route('paymentInvoices.create_invoice',$query->id) ."' class='btn btn-ghost-success'>
                        <i class='fa fa-plus'></i>
                    </a>
                </div>";
        })
        ->addColumn('request_status', function($query){
            if($query->status)
                return '<small class="badge badge-success">completed</small>';
            else
                return '<small class="badge badge-danger">pending</small>';
        })
        ->addColumn('vendor_name', function($query){
            return optional(optional($query->requestable)->vendor)->name ?? '—';
        })
        ->addColumn('lpo_total', function($query){
            $lpoout = $query->requestable;
            if (!$lpoout) return '—';
            $total = $lpoout->total_amount ?: $lpoout->amount;
            return number_format($total, 2);
        })
        ->addColumn('invoiced_amount', function($query){
            $lpoout = $query->requestable;
            if (!$lpoout) return '—';
            $invoiced = $lpoout->paymentInvoices->sum('total_amount');
            return number_format($invoiced, 2);
        })
        ->addColumn('remaining_amount', function($query){
            $lpoout = $query->requestable;
            if (!$lpoout) return '—';
            $total    = $lpoout->total_amount ?: $lpoout->amount;
            $invoiced = $lpoout->paymentInvoices->sum('total_amount');
            $remaining = max(0, $total - $invoiced);
            $class = $remaining > 0 ? 'text-success font-weight-bold' : 'text-muted';
            return '<span class="'.$class.'">'.number_format($remaining, 2).'</span>';
        })
        ->rawColumns(['action','request_status','remaining_amount']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\InvoiceRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(InvoiceRequest $model)
    {
        return $model->newQuery()->with(['requestable.vendor', 'requestable.paymentInvoices'])
                ->where('requestable_type' ,'App\Models\Lpoout')
                ->orderBy('id', 'desc');
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
            'created_at'       => new Column(['title' => __('models/invoice_requests.fields.created_at'), 'data' => 'created_at']),
            'lpoout_id'        => new Column(['title' => 'LPO No', 'data' => 'requestable.name']),
            'vendor_name'      => new Column(['title' => 'Vendor', 'data' => 'vendor_name', 'searchable' => false]),
            'lpo_total'        => new Column(['title' => 'LPO Total (AED)', 'data' => 'lpo_total', 'searchable' => false]),
            'invoiced_amount'  => new Column(['title' => 'Invoiced (AED)', 'data' => 'invoiced_amount', 'searchable' => false]),
            'remaining_amount' => new Column(['title' => 'Remaining (AED)', 'data' => 'remaining_amount', 'searchable' => false]),
            'note'             => new Column(['title' => __('models/invoice_requests.fields.note'), 'data' => 'note', 'searchable' => false]),
            'status'           => new Column(['title' => __('models/invoice_requests.fields.status'), 'data' => 'request_status', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'invoice_requests_' . time();
    }
}
