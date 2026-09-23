<?php

namespace App\DataTables;

use App\Models\Project;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Column;

class VisitTrackingDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new \Yajra\DataTables\EloquentDataTable($query);

        return $dataTable
            ->addColumn('action', 'projects.datatables_actions')
            ->addColumn('company_link', function($query) {
                if (!$query->quotation || !$query->quotation->company) return 'N/A';
                return view('components.datatables_relation_link', [
                    'id' => $query->quotation->company->id,
                    'name' => $query->quotation->company->name,
                    'model' => 'companies'
                ]);
            })
            ->addColumn('contract_value', function($query) {
                return $query->quotation ? number_format($query->quotation->amount, 2) : 'N/A';
            })
            ->addColumn('upcoming_visit', function($query) {
                $visitSchedule = $query->visitSchedules;
                $upcomingVisits = $visitSchedule->filter(function ($schedule) {
                    return $schedule->status !== 'done';
                });
                $visitDates = $upcomingVisits->map(function ($schedule) {
                    return \Carbon\Carbon::parse($schedule->visit_date);
                })->filter()->sortBy(function ($visit) {
                    return $visit->timestamp;
                });
                $currentDate = \Carbon\Carbon::now();
                $nextUpcomingVisit = $visitDates->filter(function ($visit) use ($currentDate) {
                    return $visit->isAfter($currentDate);
                })->first();
                return $nextUpcomingVisit ? $nextUpcomingVisit->toDateString() : 'N/A';
            })
            ->addColumn('end_date', function($query) {
                $startDate = \Carbon\Carbon::parse($query->date);
                return $startDate->addDays(365)->toDateString();
            })
            ->addColumn('status_tag', function($project) {
                if ($project->total_schedules_count > 0
                    && $project->done_schedules_count === $project->total_schedules_count) {
                    return '<span class="badge badge-success">Done</span>';
                } elseif ($project->overdue_schedules_count > 0) {
                    return '<span class="badge badge-danger">Overdue</span>';
                } else {
                    return '<span class="badge badge-info">Upcoming</span>';
                }
            })
            ->rawColumns(['action', 'status_tag', 'company_link']);
    }

    

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Project $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Project $model)
    {
        $query = $model->newQuery()
            ->withCount([
                'visitSchedules as total_schedules_count',
                'visitSchedules as done_schedules_count' => function ($q) {
                    $q->where('status', 'done');
                },
                'visitSchedules as overdue_schedules_count' => function ($q) {
                    $q->where('status', '!=', 'done')
                    ->whereDate('visit_date', '<', now()->toDateString());
                },
            ])
            ->with(['quotation.company', 'visitSchedules'])
            ->where('category', 'amc')
            ->where(function ($query) {
                $query->whereHas('visitSchedules', function ($q) {
                    $q->whereIn('status', ['pending', 'upcoming']);
                })
                ->orWhere(function ($q) {
                    $q->whereDoesntHave('visitSchedules', function ($innerQ) {
                        $innerQ->whereIn('status', ['pending', 'upcoming']);
                    })
                    ->whereDate('date', '>', now()->subYear());
                });
            });

            if (request()->filled('expiry_filter')) {
                $filter = request('expiry_filter');
                $now = \Carbon\Carbon::now();

                if ($filter === 'this_month') {
                    $start = $now->copy()->startOfMonth()->toDateString();
                    $end = $now->copy()->endOfMonth()->toDateString();
                    $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ? AND ?", [$start, $end]);
                } 
                elseif ($filter === 'next_month') {
                    $nextMonth = $now->copy()->addMonth();
                    $start = $nextMonth->startOfMonth()->toDateString();
                    $end = $nextMonth->endOfMonth()->toDateString();
                    $query->whereRaw("DATE_ADD(`date`, INTERVAL 1 YEAR) BETWEEN ? AND ?", [$start, $end]);
                } 
                elseif ($filter === 'visit_this_month') {
                    $start = $now->copy()->startOfMonth()->toDateString();
                    $end = $now->copy()->endOfMonth()->toDateString();
                    $query->whereHas('visitSchedules', function($q) use ($start, $end) {
                        $q->where('status', '!=', 'done')
                        ->whereBetween('visit_date', [$start, $end]);
                    });
                } 
                elseif ($filter === 'visit_next_month') {
                    $nextMonth = $now->copy()->addMonth();
                    $start = $nextMonth->startOfMonth()->toDateString();
                    $end = $nextMonth->endOfMonth()->toDateString();
                    $query->whereHas('visitSchedules', function($q) use ($start, $end) {
                        $q->where('status', '!=', 'done')
                        ->whereBetween('visit_date', [$start, $end]);
                    });
                }
            }


        return $query->orderBy('id', 'desc');
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
                'scrollX'   => true, 
                'stateSave' => true,
                'bSort' => true,
                'order'     => [[2, 'asc']],
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
            'date' => new Column([
                'title' => 'Start Date',
                'data' => 'date',
            ]),
            'company_link' => new Column([
                'title' => 'Company',
                'data' => 'company_link',
                'name' => 'quotation.company.name',
            ]),
            'contract_value' => new Column([
                'title' => 'Contract Value',
                'data' => 'contract_value',
                'orderable' => false,
                'searchable' => false,
            ]),

            'upcoming_visit' => new Column([
                'title' => 'Upcoming Visit',
                'data' => 'upcoming_visit',
            ]),
            'end_date' => new Column([
                'title' => 'End Date',
                'data' => 'end_date',
            ]),
            'status' => new Column([
                'title' => __('models/quotations.fields.status'), 
                'data' => 'status_tag',
                'searchable' => true
            ])
        ];
    }
    
    

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'visit_tracking_' . time();
    }
}
