<?php

namespace App\DataTables;

use App\Models\LabourAssignment;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Illuminate\Support\Collection;

class LabourAssignmentsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->collection($query)
            ->rawColumns(['labor_names', 'hours_combined', 'action']) // Allow HTML in these columns
            ->addColumn('project_name', function($row) {
                return isset($row['project_name']) ? $row['project_name'] : 'N/A';
            })
            ->addColumn('visit_schedule', function($row) {
                return isset($row['visit_schedule']) ? $row['visit_schedule'] : 'N/A';
            })
            ->addColumn('labor_names', function($row) {
                return isset($row['labor_names']) ? $row['labor_names'] : 'N/A';
            })
            ->addColumn('hours_combined', function($row) {
                return isset($row['hours_combined']) ? $row['hours_combined'] : 'N/A';
            })
            ->addColumn('date', function($row) {
                return isset($row['date']) ? $row['date'] : 'N/A';
            })
            ->addColumn('action', function ($row) {
                $projectId = $row['project_id'];
                $assignment_start_date = $row['assignment_start_date'];
                $url = route('attendance.deleteGroup', ['project_id' => $projectId, 'assignment_start_date' => $assignment_start_date]);
                $editUrl = route('attendance.editGroup', ['project_id' => $projectId, 'assignment_start_date' => $assignment_start_date]);
                return '
                <a href="' . $url . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this attendance group?\')">Delete</a>
                    <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>
                ';
            });
    }
    

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\LabourAssignment $model
     * @return Collection
     */
    public function query(LabourAssignment $model)
    {
        $assignments = $model->newQuery()
            ->with(['labor', 'project', 'visitSchedule'])
            ->get();
    
        $grouped = $assignments->groupBy(function ($assignment) {
            return $assignment->visit_schedule_id
                ? $assignment->project_id . '-' . $assignment->visit_schedule_id . '-' . $assignment->assignment_start_date
                : $assignment->project_id . '-' . $assignment->assignment_start_date;
        })->map(function ($group) {
            $first = $group->first();
    
            return [
                'project_id'     => $first->project_id,
                'assignment_start_date' => $first->assignment_start_date,
                'project_name'   => isset($first->project->subject) ? $first->project->subject : 'N/A',
                'visit_schedule' => isset($first->visitSchedule) ? $first->visitSchedule->visit_date : 'N/A',
                'labor_names' => '<ul style="padding-left: 16px; margin: 0;">' .
                    $group->map(function($a) {
                        $laborName = isset($a->labor->name) ? $a->labor->name : 'N/A';
                        $laborId = $a->labor_id;
                        $currentMonth = now()->format('Y-m'); 

                        $url = route('labor.monthlyReport', ['labor_id' => $laborId, 'month' => $currentMonth]);

                        return '<li><a href="' . $url . '" target="_blank">' . $laborName . '</a></li>';
                    })->join('') .
                    '</ul>',

                'hours_combined' => '<ul style="padding-left: 16px; margin: 0;">' .
                                    $group->map(function($a) { return '<li>' . (isset($a->overtime_hours) ? $a->overtime_hours : 0) . ' hrs</li>'; })->join('') .
                                    '</ul>',
                'date'           => $first->assignment_start_date ? \Carbon\Carbon::parse($first->assignment_start_date)->format('Y-m-d') : 'N/A',
            ];
        })->values();
    
        \Log::info('Grouped Data:', $grouped->toArray());
        return $grouped;
    }
    

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('labourassignments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('project_name')->title('Project Name'),
            Column::make('visit_schedule')->title('Visit Schedule'),
            Column::make('labor_names')->title('Assigned Workers'),
            Column::make('hours_combined')->title('Overtime'),
            Column::make('date')->title('Date'),
            Column::computed('action')->title('Action')
                ->exportable(false)
                ->printable(false)
                ->sortable(false)
                ->searchable(false),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'LabourAssignments_' . date('YmdHis');
    }
}