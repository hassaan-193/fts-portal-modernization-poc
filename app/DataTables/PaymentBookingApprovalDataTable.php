<?php

namespace App\DataTables;

use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\Services\PaymentBookingService;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

/**
 * The reviewers' queue: bookings waiting on a level the signed-in user can act on.
 *
 * A verifier sees bookings sitting at level 1, an approver those at level 2, and
 * someone holding both roles sees both — minus any booking they already decided the
 * other level of, which the service refuses anyway. `?filter=all` widens it to every
 * booking that has entered the chain, for context.
 */
class PaymentBookingApprovalDataTable extends DataTable
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
            ->addColumn('action', 'payment_bookings.approvals.datatables_actions')
            ->addColumn('type_tag', function ($booking) {
                $class = $booking->isCash() ? 'success' : 'primary';

                return '<span class="badge badge-' . $class . '">' . $booking->type_label . '</span>';
            })
            ->addColumn('payee_project', function ($booking) {
                $project = $booking->project_cost_centre
                    ? '<br><small class="text-muted">' . e($booking->project_cost_centre) . '</small>'
                    : '';

                return e($booking->payee) . $project;
            })
            ->addColumn('booked_amount', function ($booking) {
                return 'AED ' . number_format($booking->amount, 2);
            })
            ->addColumn('requested_by', function ($booking) {
                return $booking->creator ? e($booking->creator->name) : '—';
            })
            ->addColumn('waiting_on', function ($booking) {
                $level = $booking->pendingLevel();

                if ($level === null) {
                    return '<span class="text-muted">—</span>';
                }

                $levels = PaymentBookingApproval::levels();

                return '<span class="badge badge-warning">' . $levels[$level] . '</span>';
            })
            ->addColumn('release_on', function ($booking) {
                $date = $booking->effective_date;

                return $date ? $date->format('d M Y') : '—';
            })
            ->addColumn('status_tag', function ($booking) {
                return view('payment_bookings.status_badge', ['booking' => $booking])->render();
            })
            ->rawColumns(['action', 'type_tag', 'payee_project', 'waiting_on', 'status_tag']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PaymentBooking $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PaymentBooking $model)
    {
        // Names are all that render; selecting the whole User model would leak
        // api_token / firebase_token into the AJAX payload.
        $query = $model->newQuery()->with([
            'creator' => function ($q) {
                $q->select('id', 'name');
            },
            'approvals.user' => function ($q) {
                $q->select('id', 'name');
            },
        ]);

        // Shared with the mobile API, so the portal queue and the app's queue can
        // never drift apart on what "needs my action" means.
        PaymentBookingService::scopeQueue(
            $query,
            auth()->user(),
            request('filter') === 'all' ? 'all' : 'pending'
        );

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
            // Raw JS via the 2nd argument — the 3rd would quote the selector.
            // Raw JS via the 2nd argument — the 3rd would quote the selector.
            ->minifiedAjax('', '
            data.filter = $("input[name=filter]:checked").val();')
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
                // No stateSave here. The queue is filtered by the external radio
                // buttons, and a persisted page offset or search term from an
                // earlier visit silently leaves the table looking empty.
                'stateSave' => false,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    [
                        'extend'    => 'export',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-download"></i> ' . __('auth.app.export') . '',
                    ],
                    [
                        'extend'    => 'reload',
                        'className' => 'btn btn-default btn-sm no-corner',
                        'text'      => '<i class="fa fa-refresh"></i> ' . __('auth.app.reload') . '',
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
            'reference_no' => new Column(['title' => __('models/payment_bookings.fields.reference_no'), 'data' => 'reference_no', 'searchable' => true]),
            'booking_type' => new Column(['title' => __('models/payment_bookings.fields.booking_type'), 'data' => 'type_tag', 'searchable' => false, 'orderable' => false]),
            'payee'        => new Column(['title' => __('models/payment_bookings.fields.payee_project'), 'data' => 'payee_project', 'searchable' => true]),
            'amount'       => new Column(['title' => __('models/payment_bookings.fields.amount'), 'data' => 'booked_amount', 'searchable' => false]),
            'requested_by' => new Column(['title' => __('models/payment_bookings.fields.requested_by'), 'data' => 'requested_by', 'searchable' => false, 'orderable' => false]),
            'release_on'   => new Column(['title' => __('models/payment_bookings.fields.release_on'), 'data' => 'release_on', 'searchable' => false, 'orderable' => false]),
            'waiting_on'   => new Column(['title' => __('models/payment_bookings.fields.waiting_on'), 'data' => 'waiting_on', 'searchable' => false, 'orderable' => false]),
            'status'       => new Column(['title' => __('models/payment_bookings.fields.status'), 'data' => 'status_tag', 'searchable' => false]),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'payment_booking_approvals_' . time();
    }
}
