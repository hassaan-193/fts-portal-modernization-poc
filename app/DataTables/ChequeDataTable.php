<?php

namespace App\DataTables;

use App\Models\Cheque;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class ChequeDataTable extends DataTable
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

        return $dataTable
        ->addColumn('action', 'cheques.datatables_actions')
        ->addColumn('setType', function($query) {
            return $query->type;
        })
        ->addColumn('set_company', function($query) {
            if(isset($query->transactionable->quotation->company)){
                return view('components.datatables_relation_link', [
                    'id' => $query->transactionable->quotation->company->id,
                    'name' => $query->transactionable->quotation->company->name,
                    'model' => 'companies'
                ]);
            }else if($query->type == 'Payment'){
                if(isset($query->transactionable->lpoin->vendor)){
                    return view('components.datatables_relation_link', [
                        'id' => $query->transactionable->lpoout->vendor->id,
                        'name' => $query->transactionable->lpoout->vendor->name,
                        'model' => 'vendors'
                    ]);
                } else {
                    return view('components.datatables_relation_link', [
                        'id' => $query->id,
                        'name' => 'Expense - '.$query->note,
                        'model' => 'payments'
                    ]);
                }
            }
            
            return '';
        })
        ->addColumn('status_tag', function($query) {
            return view('components.datatables_status', [
                'msg' => ($query->status) ? 'complete' : 'pending',
                'type' => ($query->status) ? 'success' : 'danger',
            ]);
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Cheque $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Cheque $model)
    {
        return $model->newQuery()
        ->with('transaction_payment_type')
        ->whereHas('transaction_payment_type',function($q){
            $q->where('name','Cheque');
        })->where('status',0)->orderBy('id','desc');;
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
            'company_id' => new Column(['title' => 'Company / Vendor', 'data' => 'set_company','searchable' => true]),
            'type' => new Column(['title' => "Type", 'data' => 'setType', 'searchable' => false]),
            'clearance_date' => new Column(['title' => __('models/receipts.fields.clearance_date'), 'data' => 'clearance_date','searchable' => false]),
            'payment_no' => new Column(['title' => __('models/receipts.fields.payment_no'), 'data' => 'payment_no','searchable' => false]),
            'total' => new Column(['title' => __('models/receipts.fields.total'), 'data' => 'total','searchable' => false]),
            'note' => new Column(['title' => __('models/receipts.fields.note'), 'data' => 'note','searchable' => false]),
            'status' => new Column(['title' => __('models/receipts.fields.status'), 'data' => 'status_tag','searchable' => false])
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'cheques_' . time();
    }
}
