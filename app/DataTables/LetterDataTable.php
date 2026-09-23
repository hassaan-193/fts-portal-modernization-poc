<?php
namespace App\DataTables;

use App\Models\Letter;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class LetterDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'letters.datatables_actions')
            ->editColumn('type', fn($row) => ucfirst($row->type))
            ->editColumn('issued_at', fn($row) => $row->issued_at->format('Y-m-d'))
            ->addColumn('staff_name', function ($row) {
                if ($row->stafProfile) {
                    $url = route('staf.show', $row->stafProfile->id);
                    return '<a href="' . $url . '">' . e($row->stafProfile->name) . '</a>';
                }
                return 'N/A';
            })
            ->filterColumn('staff_name', function($query, $keyword) {
                $query->whereHas('stafProfile', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action', 'staff_name']);
    }    
    

    public function query(Letter $model)
    {
        return $model->newQuery()->with('stafProfile')->orderBy('id', 'desc');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'bSort'     => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm', 'text' => '<i class="fa fa-download"></i> Export'],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm', 'text' => '<i class="fa fa-refresh"></i> Reload'],
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm', 'text' => '<i class="fa fa-plus"></i> Create'],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('title')->title('Title'),
            Column::make('staff_name')->title('Staff'),
            Column::make('type')->title('Type'),
            Column::make('issued_by')->title('Issued By'),
            Column::make('issued_at')->title('Issued At'),
        ];
    }

    protected function filename()
    {
        return 'letters_' . time();
    }
}
