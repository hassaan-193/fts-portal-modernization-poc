<?php

namespace App\DataTables;

use App\Models\Quotation;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class QuotationDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'quotations.datatables_actions')
        ->addColumn('model', function($query) {
            if (!$query->company) {
                return '-';
            }
            return view('components.datatables_relation_link', [
                'id' => $query->company->id,
                'name' => $query->company->name,
                'model' => 'companies'
            ]);
        })
        ->addColumn('status_tag', function($query) {
            return view('components.datatables_status', [
                'msg' => ($query->status) ? 'approved' : 'pending',
                'type' => ($query->status) ? 'success' : 'danger',
            ]);
        })
        ->addColumn('approved_by', function($query) {
            return $query->approvedBy->name ?? '-';
        })
        ->rawColumns(['action','model','status_tag']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Quotation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Quotation $model)
    {
        return $model->newQuery()->with(['company:id,name','quotation_type','quotation_company','approvedBy:id,name'])->orderBy('id','desc')->get();
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
            'id' => new Column(['title' => __('models/quotations.fields.id'), 'data' => 'id']),
            'name' => new Column(['title' => __('models/quotations.fields.name'), 'data' => 'name']),
            'quotation_type_id' => new Column(['title' => __('models/quotations.fields.quotation_type_id'), 'data' => 'quotation_type.name']),
            'quotation_company' => new Column(['title' => __('models/quotations.fields.quotation_company'), 'data' => 'quotation_company.name']),
            'company_id' => new Column(['title' => __('models/companies.singular'), 'data' => 'model']),
            'ref_no' => new Column(['title' => __('models/quotations.fields.ref_no'), 'data' => 'ref_no','searchable' => true]),
            'subject' => new Column(['title' => __('models/quotations.fields.subject'), 'data' => 'subject','searchable' => true]),
            'amount' => new Column(['title' => __('models/quotations.fields.amount'), 'data' => 'amount','searchable' => true]),
            'date' => new Column(['title' => __('models/quotations.fields.date'), 'data' => 'date','searchable' => false]),
            'status' => new Column(['title' => __('models/quotations.fields.status'), 'data' => 'status_tag','searchable' => false]),
            'approved_by' => new Column(['title' => __('models/quotations.fields.approved_by'), 'data' => 'approved_by','searchable' => false])
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
