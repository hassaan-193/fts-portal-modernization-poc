<?php

namespace App\DataTables\CompanyCards;

use App\Models\Quotation;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;

class CompanyQuotationDataTable extends DataTable
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

        return $dataTable
        ->addColumn('status_tag', function($query) {
            return view('components.datatables_status', [
                'msg' => ($query->status) ? 'approved' : 'pending',
                'type' => ($query->status) ? 'success' : 'danger',
            ]);
        })
        ->rawColumns(['status_tag']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Quotation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Quotation $model)
    {
        return $model->newQuery()
        ->with(['company:id,name','quotation_type','quotation_company'])
        ->whereId(\Auth::guard('company')->user()->id)
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
                    ]
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
            'id' => new Column(['title' => __('models/quotations.fields.id'), 'data' => 'id']),
            'date' => new Column(['title' => __('models/quotations.fields.date'), 'data' => 'date','searchable' => false]),
            'quotation_type_id' => new Column(['title' => __('models/quotations.fields.quotation_type_id'), 'data' => 'quotation_type.name']),
            'ref_no' => new Column(['title' => __('models/quotations.fields.ref_no'), 'data' => 'ref_no','searchable' => true]),
            'amount' => new Column(['title' => __('models/quotations.fields.amount'), 'data' => 'amount','searchable' => true]),
            'status' => new Column(['title' => __('models/quotations.fields.status'), 'data' => 'status_tag','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'quotations_' . time();
    }
}
