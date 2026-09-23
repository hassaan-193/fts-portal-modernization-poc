<?php

namespace App\DataTables;

use App\Models\StafProfile;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;

class StafExpiryDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->editColumn('name', function ($row) {
                $url = route('staf.show', $row->id);
                return "<a href='{$url}'>{$row->name}</a>";
            })
            ->editColumn('passport_expiry', function ($row) {
                return $this->highlightIfExpired($row->passport_expiry);
            })
            ->editColumn('visa_expiry', function ($row) {
                return $this->highlightIfExpired($row->visa_expiry);
            })
            ->editColumn('emirates_id_expiry', function ($row) {
                return $this->highlightIfExpired($row->emirates_id_expiry);
            })
            ->editColumn('labor_card_expiry', function ($row) {
                return $this->highlightIfExpired($row->labor_card_expiry);
            })
            ->rawColumns(['name', 'passport_expiry', 'visa_expiry', 'emirates_id_expiry', 'labor_card_expiry']);
    }
    
    private function highlightIfExpired($date)
    {
        if (!$date) return '';
        
        $today = Carbon::today();
        $expiryDate = Carbon::parse($date);
    
        if ($expiryDate->isPast()) {
            return "<span style='color: red; font-weight: bold;'>$date</span>";
        }
    
        return $date;
    }
    
    public function query(StafProfile $model)
    {
        $query = $model->newQuery();
        
        // Apply date filter if present
        if (request()->has('expiry_date') && request('expiry_date')) {
            $date = request('expiry_date');
            $query->where(function ($q) use ($date) {
                $q->whereDate('passport_expiry', $date)
                  ->orWhereDate('visa_expiry', $date)
                  ->orWhereDate('emirates_id_expiry', $date)
                  ->orWhereDate('labor_card_expiry', $date);
            });
        }
        
        return $query->orderBy('id', 'DESC');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => false,
                'bSort' => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                        'extend' => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-download"></i> Export'
                    ],
                    [
                        'extend' => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text' => '<i class="fa fa-refresh"></i> Reload'
                    ],
                ],
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
            ]);
    }

    protected function getColumns()
    {
        return [
            'id' => new Column(['title' => 'ID', 'data' => 'id']),
            'name' => new Column(['title' => 'First Name', 'data' => 'name']),
            'last_name' => new Column(['title' => 'Last Name', 'data' => 'last_name']),
            'passport_expiry' => new Column(['title' => 'Passport Expiry', 'data' => 'passport_expiry']),
            'visa_expiry' => new Column(['title' => 'Visa Expiry', 'data' => 'visa_expiry']),
            'emirates_id_expiry' => new Column(['title' => 'Emirates ID Expiry', 'data' => 'emirates_id_expiry']),
            'labor_card_expiry' => new Column(['title' => 'Labor Card Expiry', 'data' => 'labor_card_expiry']),
        ];
    }

    protected function filename()
    {
        return 'staf_expiry_' . time();
    }
}