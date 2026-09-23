<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PendingAttendanceDataTable extends DataTable
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
            ->addIndexColumn()
            ->addColumn('attendance_date', function($row) {
                return $row->attendance_date->format('M d, Y');
            })
            ->addColumn('day_name', function($row) {
                return $row->attendance_date->format('l');
            })
            ->addColumn('labor_name', function($row) {
                return $row->labor->name ?? 'Unknown';
            })
            ->addColumn('foreman_name', function($row) {
                return $row->foreman->name ?? 'Unknown';
            })
            ->addColumn('status_badge', function($row) {
                $statusMap = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                ];
                $badgeClass = $statusMap[$row->status] ?? 'secondary';
                return '<span class="badge badge-'.$badgeClass.'">'.ucfirst($row->status).'</span>';
            })
            ->addColumn('informed_status', function($row) {
                $approval = $row->approvals()->latest()->first();
                return $approval ? ucfirst($approval->informed) : 'N/A';
            })
            ->addColumn('specific_reason', function($row) {
                $approval = $row->approvals()->latest()->first();
                return $approval ? ($approval->specific_reason ?? '-') : '-';
            })
            ->addColumn('action', function($row) {
                return view('pending_attendances.datatables_actions', [
                    'id' => $row->id,
                    'status' => $row->status
                ])->render();
            })
            ->rawColumns(['status_badge', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Attendance $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Attendance $model)
    {
        $search = request('search.value', '');
        
        $query = $model->newQuery()
            ->with(['labor', 'foreman', 'approvals']);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('labor', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('foreman', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('attendance_date', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
            });
        }
        
        return $query->orderBy('attendance_date', 'desc');
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
            ->minifiedAjax(route('pending.attendance.data'))
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
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
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false),
            Column::make('attendance_date')->title('Date'),
            Column::make('day_name')->title('Day'),
            Column::make('labor_name')->title('Labor'),
            Column::make('foreman_name')->title('Foreman'),
            Column::make('status_badge')->title('Status'),
            Column::make('informed_status')->title('Informed'),
            Column::make('specific_reason')->title('Reason'),
            Column::make('action')->title('Action')->orderable(false)->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PendingAttendance_' . date('YmdHis');
    }
}
