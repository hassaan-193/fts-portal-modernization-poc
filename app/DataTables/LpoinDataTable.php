<?php

namespace App\DataTables;

use App\Models\Lpoin;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class LpoinDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'lpoins.datatables_actions')
        ->addColumn('model', function($query) {
            $type = $query->quotation->quotation_type ? $query->quotation->quotation_type->name :'';
            return view('components.datatables_relation_link', [
                'id' => $query->quotation->id,
                'name' => $query->quotation->name,
                'model' => 'quotations'
            ]);
        })
        ->rawColumns(['action','model']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lpoin $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lpoin $model)
    {
        $query = $model->newQuery()
            ->with('quotation.quotation_type','quotation.quotation_company')
            ->orderBy('id','desc')
            ->get();
        if (request()->date_from) {
            $query = $query->filter(function($item) {
                return $item->date_issue >= request()->date_from;
            });
        }
        if (request()->date_to) {
            $query = $query->filter(function($item) {
                return $item->date_issue <= request()->date_to;
            });
        }
        return $query;
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
            ->ajax([
                'url'  => route('lpoins.index'),
                'type' => 'GET',
                'data' => 'function(d) {
                    d.date_from = $("#date_from").val();
                    d.date_to   = $("#date_to").val();
                }'
            ])
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'scrollX'   => true,
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
            'id' => new Column(['title' => __('models/lpoins.fields.id'), 'data' => 'id']),
            'ref_no' => new Column(['title' => __('models/lpoins.fields.ref_no'), 'data' => 'ref_no',]),
            'quotation_id' => new Column(['title' => __('models/quotations.singular'), 'data' => 'model', 'name' => "quotation.name"]),
            'quotation_type' => new Column(['title' => __('models/quotations.fields.quotation_type_id'), 'data' => 'quotation.quotation_type.name']),
            'quotation_company' => new Column(['title' => __('models/quotations.fields.quotation_company'), 'data' => 'quotation.quotation_company.name']),
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
