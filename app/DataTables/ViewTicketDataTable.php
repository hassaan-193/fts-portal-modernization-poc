<?php

namespace App\DataTables;

use App\Models\Ticket;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class ViewTicketDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            // Display the staff name from the related stafProfile
            ->editColumn('staf_name', function (Ticket $ticket) {
                return $ticket->stafProfile->name ?? 'N/A';
            })
            // Show the return date only for two-way tickets
            ->editColumn('return_date', function (Ticket $ticket) {
                return $ticket->travel_type == 'Two Way' ? $ticket->return_date : '-';
            })
            // Add an actions column with the small update button
            ->addColumn('actions', function (Ticket $ticket) {
                $button = '<form action="' . route('tickets.updateStatus', $ticket->id) . '" method="POST" style="display:inline;">'
                    . csrf_field()
                    . '<input type="hidden" name="status" value="release">'
                    . '<button type="submit" class="btn rounded-pill ' 
                    . ($ticket->payment_status == 'release' ? 'btn-success' : 'btn-danger') . '" '
                    . 'title="Release Ticket" style="padding: 0.15rem 0.3rem; font-size: 0.65rem;" '
                    . ($ticket->payment_status == 'release' ? 'disabled' : '') . '>'
                    . '<i class="fa fa-check"></i>'
                    . '</button>'
                    . '</form>';
                return $button;
            })
            ->rawColumns(['actions']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Ticket $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ticket $model)
    {
        return $model->newQuery()->with('stafProfile');
    }

    /**
     * Optional method if you want to use the HTML builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('tickets-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
            ->buttons(
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id')->title('Ticket ID'),
            Column::computed('staf_name')->title('Staff Name'),
            Column::make('agent_name')->title('Agent Name'),
            Column::make('travel_type')->title('Travel Type'),
            Column::make('travel_date')->title('Travel Date'),
            Column::computed('return_date')->title('Return Date'),
            Column::make('payment_status')->title('Status'),
            Column::computed('actions')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Tickets_' . date('YmdHis');
    }
}
