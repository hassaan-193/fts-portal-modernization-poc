<?php

namespace App\DataTables;

use App\Models\Company;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class CompanyDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'companies.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Company $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $model)
    {
        return $model->newQuery()->orderBy('id','desc');;
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
            'id' => new Column(['title' => __('models/companies.fields.id'), 'data' => 'id']),
            'name' => new Column(['title' => __('models/companies.fields.name'), 'data' => 'name']),
            'email' => new Column(['title' => __('models/companies.fields.email'), 'data' => 'email']),
            'contact_person' => new Column(['title' => __('models/companies.fields.contact_person'), 'data' => 'contact_person','searchable' => false]),
            'contact_no' => new Column(['title' => __('models/companies.fields.contact_no'), 'data' => 'contact_no','searchable' => false]),
            'vat_no' => new Column(['title' => __('models/companies.fields.vat_no'), 'data' => 'vat_no','searchable' => false]),
            'billing_address' => new Column(['title' => __('models/companies.fields.billing_address'), 'data' => 'billing_address','searchable' => false]),
            'billing_contact_person' => new Column(['title' => __('models/companies.fields.billing_contact_person'), 'data' => 'billing_contact_person','searchable' => false]),
            'billing_pob' => new Column(['title' => __('models/companies.fields.billing_pob'), 'data' => 'billing_pob','searchable' => false]),
            'billing_email' => new Column(['title' => __('models/companies.fields.billing_email'), 'data' => 'billing_email','searchable' => false]),
            'shipping_address' => new Column(['title' => __('models/companies.fields.shipping_address'), 'data' => 'shipping_address','searchable' => false]),
            'shipping_contact_person' => new Column(['title' => __('models/companies.fields.shipping_contact_person'), 'data' => 'shipping_contact_person','searchable' => false]),
            'shipping_pob' => new Column(['title' => __('models/companies.fields.shipping_pob'), 'data' => 'shipping_pob','searchable' => false]),
            'shipping_email' => new Column(['title' => __('models/companies.fields.shipping_email'), 'data' => 'shipping_email','searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'companies_' . time();
    }
}
