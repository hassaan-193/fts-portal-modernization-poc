<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Facades\DataTables;

class PresentsAttendanceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return DataTables::collection($query)
            ->addIndexColumn()
            ->addColumn('formatted_date', function($row) {
                try {
                    $date = is_string($row->attendance_date) 
                        ? \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date)
                        : \Carbon\Carbon::parse($row->attendance_date);
                    return $date->format('M d, Y');
                } catch (\Exception $e) {
                    return $row->attendance_date ?? '';
                }
            })
            ->addColumn('day_name', function($row) {
                try {
                    $date = is_string($row->attendance_date) 
                        ? \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date)
                        : \Carbon\Carbon::parse($row->attendance_date);
                    return $date->format('l');
                } catch (\Exception $e) {
                    return '';
                }
            })
            ->addColumn('formatted_overtime', function($row) {
                return is_numeric($row->overtime_hours) 
                    ? number_format((float)$row->overtime_hours, 2)
                    : ($row->overtime_hours ?? '0.00');
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Attendance $model
     * @return \Illuminate\Support\Collection
     */
    public function query(Attendance $model)
    {
        $search = request('search.value', '');
        
        $query = \DB::table('attendances')
            ->join('attendance_labor_details', 'attendances.id', '=', 'attendance_labor_details.attendance_id')
            ->join('staf_profile', 'attendance_labor_details.labor_id', '=', 'staf_profile.id')
            ->leftJoin('users', 'attendances.foreman_id', '=', 'users.id')
            ->leftJoin('sites', 'attendance_labor_details.site_id', '=', 'sites.id')
            ->select(
                'attendances.id',
                'attendances.attendance_date',
                'staf_profile.name as labor_name',
                'users.name as foreman_name',
                \DB::raw('COALESCE(sites.site_name, attendance_labor_details.custom_site_name) as site_name'),
                'attendance_labor_details.overtime_hours'
            )
            ->whereNotNull('attendance_labor_details.overtime_hours')
            ->whereNotNull('attendance_labor_details.labor_id')
            ->orderBy('attendances.attendance_date', 'desc');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('staf_profile.name', 'like', '%' . $search . '%')
                  ->orWhere('users.name', 'like', '%' . $search . '%')
                  ->orWhere('sites.site_name', 'like', '%' . $search . '%')
                  ->orWhere('attendance_labor_details.custom_site_name', 'like', '%' . $search . '%');
            });
        }
        
        return collect($query->get());
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
            ->parameters([
                'dom'       => '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
                'stateSave' => false,
                'order'     => [[1, 'desc']],
                'buttons'   => [
                    [
                       'extend' => 'excel',
                       'className' => 'btn btn-default btn-sm no-corner',
                       'text' => '<i class="fa fa-download"></i> Export'
                    ],
                    [
                       'extend' => 'reload',
                       'className' => 'btn btn-default btn-sm no-corner',
                       'text' => '<i class="fa fa-refresh"></i> Reload'
                    ],
                ],
                'processing' => false,
                'serverSide' => false,
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
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(40),
            Column::make('formatted_date')->title('Date')->orderable(false)->searchable(false)->width(100),
            Column::make('day_name')->title('Day')->searchable(false)->orderable(false)->width(80),
            Column::make('labor_name')->title('Labor')->searchable(true)->width(150),
            Column::make('foreman_name')->title('Foreman')->searchable(true)->width(150),
            Column::make('site_name')->title('Site Name')->searchable(true)->width(150),
            Column::make('formatted_overtime')->title('Overtime (Hours)')->orderable(false)->searchable(false)->width(120),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PresentsAttendance_' . date('YmdHis');
    }
}
