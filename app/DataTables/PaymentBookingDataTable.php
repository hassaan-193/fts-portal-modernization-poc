<?php

namespace App\DataTables;

use App\Models\PaymentBooking;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

/**
 * The accountant's own booking history.
 *
 * Scoped to the signed-in user's entries unless they hold a review permission —
 * a verifier or approver needs the whole ledger to make sense of what they sign off.
 * The `released` column is derived (approved + effective date reached), so it stays
 * consistent with the dashboard figures without a stored flag to keep in sync.
 */
class PaymentBookingDataTable extends DataTable
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
            ->addColumn('action', 'payment_bookings.datatables_actions')
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
            ->addColumn('released_amount', function ($booking) {
                $class = $booking->is_released ? 'text-success font-weight-bold' : 'text-muted';

                return '<span class="' . $class . '">AED ' . number_format($booking->released_amount, 2) . '</span>';
            })
            ->addColumn('release_on', function ($booking) {
                $date = $booking->effective_date;

                return $date ? $date->format('d M Y') : '—';
            })
            ->addColumn('status_tag', function ($booking) {
                return view('payment_bookings.status_badge', ['booking' => $booking])->render();
            })
            ->editColumn('booking_date', function ($booking) {
                return $booking->booking_date ? $booking->booking_date->format('d M Y') : '—';
            })
            ->rawColumns(['action', 'type_tag', 'payee_project', 'released_amount', 'status_tag']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PaymentBooking $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PaymentBooking $model)
    {
        // Only the creator's name is rendered, and the full User model would carry
        // api_token / firebase_token into the AJAX payload — so constrain the select.
        $query = $model->newQuery()->with(['creator' => function ($q) {
            $q->select('id', 'name');
        }]);

        // Reviewers see everything; everyone else only their own bookings.
        if (!auth()->user()->can('verify_payment_bookings') && !auth()->user()->can('approve_payment_bookings')) {
            $query->ownedBy(auth()->id());
        }

        if (request()->filled('booking_type')
            && array_key_exists(request('booking_type'), PaymentBooking::types())) {
            $query->ofType(request('booking_type'));
        }

        // Guard against a non-numeric value casting to 0 and silently filtering the
        // list down to drafts.
        if (request()->filled('status') && request('status') !== 'all'
            && array_key_exists((int) request('status'), PaymentBooking::statuses())
            && ctype_digit((string) request('status'))) {
            $query->where('status', (int) request('status'));
        }

        if (request()->filled('date_from') && strtotime(request('date_from')) !== false) {
            $query->whereDate('booking_date', '>=', request('date_from'));
        }

        if (request()->filled('date_to') && strtotime(request('date_to')) !== false) {
            $query->whereDate('booking_date', '<=', request('date_to'));
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
            // The filters go in as the 2nd argument (raw JS). The 3rd argument
            // quotes its values, which would post the selector source as a literal
            // string instead of the field's value.
            ->minifiedAjax('', '
            data.booking_type = $("#filter_booking_type").val();
            data.status = $("#filter_status").val();
            data.date_from = $("#date_from").val();
            data.date_to = $("#date_to").val();')
            ->addAction(['width' => '120px', 'printable' => false, 'title' => __('crud.action')])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
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
            'booking_date' => new Column(['title' => __('models/payment_bookings.fields.booking_date'), 'data' => 'booking_date', 'searchable' => false]),
            'booking_type' => new Column(['title' => __('models/payment_bookings.fields.booking_type'), 'data' => 'type_tag', 'searchable' => false, 'orderable' => false]),
            'payee'        => new Column(['title' => __('models/payment_bookings.fields.payee_project'), 'data' => 'payee_project', 'searchable' => true]),
            'amount'       => new Column(['title' => __('models/payment_bookings.fields.booked'), 'data' => 'booked_amount', 'searchable' => false]),
            'released'     => new Column(['title' => __('models/payment_bookings.fields.released'), 'data' => 'released_amount', 'searchable' => false, 'orderable' => false]),
            'release_on'   => new Column(['title' => __('models/payment_bookings.fields.release_on'), 'data' => 'release_on', 'searchable' => false, 'orderable' => false]),
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
        return 'payment_bookings_' . time();
    }
}
