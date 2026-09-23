<?php
namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class OrdersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'orders.datatables_actions')
            ->addColumn('vendor_name', function ($order) {
                return $order->vendor->name ?? '';
            })
            ->rawColumns(['action']);
    }

    public function query(Order $model)
    {
        return $model->newQuery()->with('vendor');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->parameters([
                'dom' => 'Bfrtip',
                'order' => [[0, 'desc']],
                'buttons' => ['create', 'export', 'print', 'reset', 'reload'],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('date'),
            Column::make('trn'),
            Column::make('ref_no'),
            new Column(['title' => 'Vendor', 'data' => 'vendor_name', 'name' => 'vendor.name']),
            Column::make('attn'),
            Column::make('contact'),
        ];
    }

    protected function filename()
    {
        return 'Orders_' . date('YmdHis');
    }
}
