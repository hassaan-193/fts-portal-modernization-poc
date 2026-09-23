<?php

namespace App\DataTables\CompanyCards;

use App\Models\Lpoin;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class CompanyLpoinDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new CollectionDataTable($query);

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lpoin $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lpoin $model)
    {
        return $model->newQuery()
        ->with('quotation.quotation_type','quotation.quotation_company')
        ->whereHas('quotation',function($query){
            $query->whereId(\Auth::guard('company')->user()->id);
        })
        ->orderBy('id','desc')
        ->get();
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
            ->ajax([
                'data'=> 'function(d){
                    d.table= "companyLpoinDataTable";
                }'
            ])
            ->parameters([
                'dom'       => '<"row" <"col-md-8"B> <"col-md-4"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
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
            'id' => new Column(['title' => __('models/lpoins.fields.id'), 'data' => 'id']),
            'ref_no' => new Column(['title' => __('models/lpoins.fields.ref_no'), 'data' => 'ref_no',]),
            'quotation_type' => new Column(['title' => __('models/quotations.fields.quotation_type_id'), 'data' => 'quotation.quotation_type.name']),
            'date_issue' => new Column(['title' => __('models/lpoins.fields.date_issue'), 'data' => 'date_issue','searchable' => false]),
            'contract_value' => new Column(['title' => __('models/lpoins.fields.contract_value'), 'data' => 'quotation.amount','searchable' => false]),
            'civil_defence_fee' => new Column(['title' => __('models/lpoins.fields.civil_defence_fee'), 'data' => 'civil_defence_fee','searchable' => true]),
            'government_fee' => new Column(['title' => __('models/lpoins.fields.government_fee'), 'data' => 'government_fee','searchable' => true]),
            'adjustment_fee' => new Column(['title' => __('models/lpoins.fields.adjustment_fee'), 'data' => 'adjustment_fee','searchable' => true])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'lpoins_' . time();
    }
}
