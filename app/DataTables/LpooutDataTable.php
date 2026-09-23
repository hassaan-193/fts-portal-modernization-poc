<?php

namespace App\DataTables;

use App\Models\Lpoout;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class LpooutDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'lpoouts.datatables_actions')
        ->addColumn('model', function($query) {
            return view('components.datatables_relation_link', [
                'id' => $query->vendor->id,
                'name' => $query->vendor->name,
                'model' => 'vendors'
            ]);
        })
        ->addColumn('quotation_ref', function($row) {
            $quotation = optional($row->project)->quotation ?? null;
            if ($quotation && $quotation->id) {
                $label = htmlspecialchars($quotation->ref_no ?? 'View');
                return '<a href="' . route('quotations.show', $quotation->id) . '">' . $label . '</a>';
            }
            return '-';
        })
        ->editColumn('lpo_invoice_no', function($row) {
            $quotation = optional($row->project)->quotation ?? null;
            if ($quotation && $quotation->id) {
                $label = htmlspecialchars($row->lpo_invoice_no);
                return '<a href="' . route('quotations.show', $quotation->id) . '">' . $label . '</a>';
            }
            return htmlspecialchars($row->lpo_invoice_no);
        })
        ->rawColumns(['action', 'model', 'quotation_ref', 'lpo_invoice_no']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lpoout $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lpoout $model)
    {
        return $model->newQuery()->where('is_latest_revision', true)->with('project.quotation')->orderBy('id', 'desc');
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
            'lpo_invoice_no' => new Column(['title' => __('models/lpoouts.fields.lpo_invoice_no'), 'data' => 'lpo_invoice_no']),
            'name' => new Column(['title' => __('models/lpoouts.fields.name'), 'data' => 'name']),
            'lpo_type_out' => new Column(['title' => __('models/lpoOutTypes.singular'), 'data' => 'lpo_out_type.name']),
            'vendor_id' => new Column(['title' => __('models/vendors.singular'), 'data' => 'model', 'name' => 'vendor.name']),
            'quotation_ref' => new Column(['title' => 'Quotation', 'data' => 'quotation_ref', 'orderable' => false, 'searchable' => false]),
            'date' => new Column(['title' => __('models/lpoouts.fields.date'), 'data' => 'date']),
            'amount' => new Column(['title' => __('models/lpoouts.fields.amount'), 'data' => 'total_amount', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'lpoouts_' . time();
    }
}
