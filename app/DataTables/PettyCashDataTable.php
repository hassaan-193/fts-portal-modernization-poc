<?php

namespace App\DataTables;

use App\Models\PettyCash;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PettyCashDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'petty_cashes.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PettyCash $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PettyCash $model)
    {
        return $model->newQuery()->with(['account','user','payment_type','vendor:id,name'])->orderBy('id','desc');;
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
            'id' => new Column(['title' => __('models/petty_cashes.fields.id'), 'data' => 'id','searchable' => false]),
            'date_time' => new Column(['title' => __('models/petty_cashes.fields.date_time'), 'data' => 'date_time']),
            'account_id' => new Column(['title' => __('models/petty_cashes.fields.account_id'), 'data' => 'account.type']),
            'vendor_id' => new Column(['title' => __('models/petty_cashes.fields.vendor_id'), 'data' => 'vendor.name']),
            'user_id' => new Column(['title' => __('models/petty_cashes.fields.user_id'), 'data' => 'user.name']),
            'type' => new Column(['title' => __('models/petty_cashes.fields.type'), 'data' => 'payment_type.name','searchable' => false]),
            'vat' => new Column(['title' => __('models/petty_cashes.fields.vat'), 'data' => 'vat','searchable' => false]),
            'total_amount' => new Column(['title' => __('models/petty_cashes.fields.total_amount'), 'data' => 'total_amount','searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'petty_cashes_' . time();
    }
}
