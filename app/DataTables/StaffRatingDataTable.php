<?php
namespace App\DataTables;
use App\Models\StafProfile;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class StaffRatingDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('staff_name', fn($row) => $row->name)
            ->addColumn('ratings', function ($row) {
                if ($row->ratings->isEmpty()) {
                    return '<span class="text-muted">No ratings</span>';
                }

                $ratingsHtml = $row->ratings->map(function ($r) {
                    return '<div class="border p-2 rounded mr-2 mb-2 small" style="min-width: 250px; flex-shrink: 0;">
                        <strong>' . e($r->engineer->name) . '</strong><br>
                        <span>Safety: <strong>' . $r->safety_compliance . '</strong>, </span>
                        <span>Comm: <strong>' . $r->communication . '</strong>, </span>
                        <span>Attend: <strong>' . $r->attendance . '</strong></span><br>
                        <span>Time: <strong>' . $r->time_management . '</strong>, </span>
                        <span>Resp: <strong>' . $r->job_responsibility . '</strong></span><br>
                        <span>Mat: <strong>' . $r->material_handling . '</strong>, </span>
                        <span>Doc: <strong>' . $r->document_handling . '</strong>, </span>
                        <span>Comp: <strong>' . $r->competency . '</strong></span>
                    </div>';
                })->implode('');

                return '<div style="display: flex; overflow-x: auto; max-width: 100%;">' . $ratingsHtml . '</div>';
            })
            ->rawColumns(['ratings']);
    }

    public function query(StafProfile $model)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        return $model->newQuery()
            ->where('staf_type', 'Labor')
            ->with(['ratings' => function ($q) use ($month, $year) {
                $q->where('month', $month)->where('year', $year)->with('engineer');
            }]);
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
            new Column(['title' => 'Ratings by Engineers', 'data' => 'ratings']),
        ];
    }

    protected function filename()
    {
        return 'staff_ratings_' . time();
    }
}
