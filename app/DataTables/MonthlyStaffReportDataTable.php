<?php
namespace App\DataTables;
use App\Models\MonthlyStaffReport;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class MonthlyStaffReportDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('staff_name', fn($report) => $report->staff->name ?? '-')
            ->addColumn('final_score', fn($report) => '<strong>' . $report->final_score . '</strong>')
            ->rawColumns(['final_score']);
    }

    public function query(MonthlyStaffReport $model)
    {
        $month = $this->month ?? now()->month;
        $year = $this->year ?? now()->year;

        return $model->newQuery()
            ->where('month', $month)
            ->where('year', $year)
            ->with('staff');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'asc']],
                'buttons' => [
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-refresh"></i> Reload'],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-download"></i> Export'],
                ],
            ]);
    }

    protected function getColumns()
    {
        return [
            new Column(['title' => 'Labor Name', 'data' => 'staff_name']),
            new Column(['title' => 'Safety', 'data' => 'safety_avg']),
            new Column(['title' => 'Communication', 'data' => 'communication_avg']),
            new Column(['title' => 'Attendance', 'data' => 'attendance_avg']),
            new Column(['title' => 'Time', 'data' => 'time_management_avg']),
            new Column(['title' => 'Responsibility', 'data' => 'job_responsibility_avg']),
            new Column(['title' => 'Material Handling', 'data' => 'material_handling_avg']),
            new Column(['title' => 'Document Handling', 'data' => 'document_handling_avg']),
            new Column(['title' => 'Competency', 'data' => 'competency_avg']),
            new Column(['title' => 'Final Score', 'data' => 'final_score']),
        ];
    }

    protected function filename()
    {
        return 'MonthlyStaffReport_' . time();
    }
}
