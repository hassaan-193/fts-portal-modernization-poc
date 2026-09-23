<?php

namespace App\DataTables;

use App\Models\Ticket;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class TicketDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
        ->addColumn('staff_name', function ($ticket) {
            if ($ticket->stafProfile) {
                $url = url("/staf/" . $ticket->stafProfile->id);
                return '<a href="' . $url . '" target="_blank">' . e($ticket->stafProfile->name) . '</a>';
            }
            return 'N/A';
        })
            ->addColumn('action', 'tickets.datatables_actions') 
            // ->rawColumns(['action','quotation_link']); 
            ->rawColumns(['staff_name', 'action', 'quotation_link']); // Ensure 'staff_name' is processed as HTML

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Ticket $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ticket $model)
    {
        return $model->newQuery()
        ->with('stafProfile')
        ->orderBy('ticket_date', 'desc');
    }

    /**
     * Optional method if you want to use HTML builder.
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

            'ticket_date' => new Column(['title' => 'Ticket Date', 'data' => 'ticket_date']),
            'staff_name' => new Column(['title' => 'Staff Name', 'data' => 'staff_name']), // New column
            'agent_name' => new Column(['title' => 'Agent Name', 'data' => 'agent_name']),
            'travel_type' => new Column(['title' => 'Travel Type', 'data' => 'travel_type']),
            'travel_date' => new Column(['title' => 'Travel Date', 'data' => 'travel_date']),
            'return_date' => new Column(['title' => 'Return Date', 'data' => 'return_date']),
            'amount' => new Column(['title' => 'Amount', 'data' => 'amount']),
            'vat' => new Column(['title' => 'VAT (5%)', 'data' => 'vat']),
            'total_value' => new Column(['title' => 'Total Value', 'data' => 'total_value']),
            'payment_status' => new Column(['title' => 'Payment Status', 'data' => 'payment_status']),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'tickets_' . time();
    }
}
