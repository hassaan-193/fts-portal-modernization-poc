<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\DataTables\PaymentBookingDataTable;
use App\DataTables\PaymentBookingApprovalDataTable;
use App\Http\Requests\CreatePaymentBookingRequest;
use App\Http\Requests\UpdatePaymentBookingRequest;
use App\Repositories\PaymentBookingRepository;
use App\Services\PaymentBookingService;
use Response;

/**
 * Payment Bookings — recording cheque and cash payments, and the two-level review
 * that has to clear before one counts as released.
 *
 * Access is permission-driven: `paymentBookings` opens the module, while
 * `verify_payment_bookings` / `approve_payment_bookings` open the review queue and
 * put the holder on the roster for that level (see PaymentBookingService).
 */
class PaymentBookingController extends AppBaseController
{
    /** @var  PaymentBookingRepository */
    private $paymentBookingRepository;

    /**
     * Actions that need the `paymentBookings` module permission — everything that
     * creates or changes a booking.
     *
     * Read-only actions (index, show, approvals, dashboard) are deliberately absent:
     * a reviewer holds only a review permission, and still has to be able to open the
     * list and a booking in order to sign one off. PaymentBookingDataTable widens the
     * query for them; the row buttons stay hidden because they check ownership.
     */
    private $bookingActions = ['create', 'store', 'edit', 'update', 'destroy', 'submit'];

    public function __construct(PaymentBookingRepository $paymentBookingRepo)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->guardAccess($request->route() ? $request->route()->getActionMethod() : null);

            return $next($request);
        });

        $this->paymentBookingRepository = $paymentBookingRepo;
    }

    /**
     * A user reaches this module either by holding `paymentBookings` (they book
     * payments) or by sitting on a review roster (they sign them off). Booking
     * actions need the former specifically.
     */
    private function guardAccess($action)
    {
        $user      = auth()->user();
        $canBook   = $user && $user->can('paymentBookings');
        $canReview = !empty(PaymentBookingService::levelsForUser($user));

        if (in_array($action, $this->bookingActions, true)) {
            if (!$canBook) {
                abort(403, 'You do not have permission to book payments.');
            }

            return;
        }

        if (!$canBook && !$canReview) {
            abort(403, 'You do not have permission to access payment bookings.');
        }
    }

    // ---------------------------------------------------------------------
    // CRUD
    // ---------------------------------------------------------------------

    /**
     * Display a listing of the PaymentBooking.
     *
     * @param PaymentBookingDataTable $paymentBookingDataTable
     * @return Response
     */
    public function index(PaymentBookingDataTable $paymentBookingDataTable)
    {
        return $paymentBookingDataTable->render('payment_bookings.index', [
            'summary'  => PaymentBookingService::summary($this->scopeUserId()),
            'statuses' => PaymentBooking::statuses(),
            'types'    => PaymentBooking::types(),
        ]);
    }

    /**
     * Show the form for creating a new PaymentBooking.
     *
     * @return Response
     */
    public function create()
    {
        return view('payment_bookings.create', [
            'nextChequeReference' => PaymentBookingService::nextReference(PaymentBooking::TYPE_CHEQUE),
            'nextCashReference'   => PaymentBookingService::nextReference(PaymentBooking::TYPE_CASH),
        ]);
    }

    /**
     * Store a newly created PaymentBooking in storage.
     *
     * @param CreatePaymentBookingRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentBookingRequest $request)
    {
        // "Save as draft" keeps the entry out of the reviewers' queue until the
        // accountant is ready; the primary button submits it straight away.
        $submit = !$request->has('save_draft');

        $booking = PaymentBookingService::store($request->all(), auth()->id(), $submit);

        Flash::success(__('messages.saved', ['model' => __('models/payment_bookings.singular')])
            . ' (' . $booking->reference_no . ')');

        return redirect(route('payment-bookings.index'));
    }

    /**
     * Display the specified PaymentBooking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $booking = $this->findVisible($id);

        if (!$booking) {
            return redirect(route('payment-bookings.index'));
        }

        list($canDecide) = PaymentBookingService::canDecide($booking);

        return view('payment_bookings.show', [
            'paymentBooking' => $booking,
            'chain'          => PaymentBookingService::chain($booking),
            'canDecide'      => $canDecide,
        ]);
    }

    /**
     * Show the form for editing the specified PaymentBooking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $booking = $this->findVisible($id);

        if (!$booking) {
            return redirect(route('payment-bookings.index'));
        }

        if (!$booking->isEditable()) {
            Flash::error('This booking can no longer be edited — it has already been reviewed.');

            return redirect(route('payment-bookings.show', $id));
        }

        return view('payment_bookings.edit')->with('paymentBooking', $booking);
    }

    /**
     * Update the specified PaymentBooking in storage.
     *
     * @param  int $id
     * @param UpdatePaymentBookingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentBookingRequest $request)
    {
        $booking = $this->findVisible($id);

        if (!$booking) {
            return redirect(route('payment-bookings.index'));
        }

        if (!$booking->isEditable()) {
            Flash::error('This booking can no longer be edited — it has already been reviewed.');

            return redirect(route('payment-bookings.show', $id));
        }

        PaymentBookingService::update($booking, $request->all());

        Flash::success(__('messages.updated', ['model' => __('models/payment_bookings.singular')]));

        return redirect(route('payment-bookings.index'));
    }

    /**
     * Remove the specified PaymentBooking from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $booking = $this->findVisible($id);

        if (!$booking) {
            return redirect(route('payment-bookings.index'));
        }

        // An approved booking is a financial record — it stays.
        if ($booking->status === PaymentBooking::STATUS_APPROVED) {
            Flash::error('An approved booking cannot be deleted.');

            return redirect(route('payment-bookings.index'));
        }

        $booking->delete();

        Flash::success(__('messages.deleted', ['model' => __('models/payment_bookings.singular')]));

        return redirect(route('payment-bookings.index'));
    }

    // ---------------------------------------------------------------------
    // Dashboard
    // ---------------------------------------------------------------------

    /**
     * Released / upcoming figures, bucketed by the month the money actually moves.
     */
    public function dashboard(Request $request)
    {
        $userId = $this->scopeUserId();
        $months = (int) $request->input('months', 6);
        $months = max(3, min($months, 12));

        $fromMonth = $request->input('from_month');
        $toMonth = $request->input('to_month');

        return view('payment_bookings.dashboard', [
            'summary'  => PaymentBookingService::summary($userId),
            'schedule' => PaymentBookingService::releaseSchedule($userId, $months, null, $fromMonth, $toMonth),
            'months'   => $months,
            'scoped'   => $userId !== null,
            'recent'   => PaymentBookingService::upcomingReleases($userId),
            'fromMonth' => $fromMonth,
            'toMonth' => $toMonth,
        ]);
    }

    // ---------------------------------------------------------------------
    // Review chain
    // ---------------------------------------------------------------------

    /**
     * The reviewers' queue.
     */
    public function approvals(PaymentBookingApprovalDataTable $dataTable, Request $request)
    {
        $this->guardReviewer();

        return $dataTable->render('payment_bookings.approvals.index', [
            'filter' => $request->get('filter') === 'all' ? 'all' : 'pending',
            'levels' => PaymentBookingService::levelsForUser(),
        ]);
    }

    public function approve(Request $request, $id)
    {
        return $this->decide($request, $id, PaymentBookingApproval::DECISION_APPROVED);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:2000'], [
            'note.required' => 'Please say why this booking is being rejected.',
        ]);

        return $this->decide($request, $id, PaymentBookingApproval::DECISION_REJECTED);
    }

    public function hold(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:2000'], [
            'note.required' => 'Please say why this booking is being put on hold.',
        ]);

        return $this->decide($request, $id, PaymentBookingApproval::DECISION_HOLD);
    }

    /**
     * Put a held booking back in the queue at the level that parked it.
     */
    public function resume($id)
    {
        $this->guardReviewer();

        $booking = PaymentBooking::with('approvals')->find($id);

        if (!$booking) {
            Flash::error(__('models/payment_bookings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('payment-bookings.approvals'));
        }

        if ($booking->status !== PaymentBooking::STATUS_ON_HOLD) {
            Flash::error('Only a booking on hold can be resumed.');

            return redirect()->back();
        }

        PaymentBookingService::resume($booking);

        Flash::success('Booking ' . $booking->reference_no . ' is back in the review queue.');

        return redirect()->back();
    }

    /**
     * Re-submit a draft or rejected booking for review.
     */
    public function submit($id)
    {
        $booking = $this->findVisible($id);

        if (!$booking) {
            return redirect(route('payment-bookings.index'));
        }

        if (!in_array($booking->status, [PaymentBooking::STATUS_DRAFT, PaymentBooking::STATUS_REJECTED], true)) {
            Flash::error('Only a draft or rejected booking can be submitted.');

            return redirect()->back();
        }

        PaymentBookingService::submit($booking);

        Flash::success('Booking ' . $booking->reference_no . ' has been submitted for review.');

        return redirect(route('payment-bookings.show', $booking->id));
    }

    // ---------------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------------

    /**
     * Record one decision, after checking this user may make it on this booking.
     */
    private function decide(Request $request, $id, $decision)
    {
        $this->guardReviewer();

        $booking = PaymentBooking::with('approvals')->find($id);

        if (!$booking) {
            Flash::error(__('models/payment_bookings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('payment-bookings.approvals'));
        }

        list($allowed, $reason) = PaymentBookingService::canDecide($booking);

        if (!$allowed) {
            Flash::error($reason);

            return redirect()->back();
        }

        $booking = PaymentBookingService::decide($booking, auth()->id(), $decision, $request->input('note'));

        Flash::success('Your decision was saved. ' . $booking->reference_no . ' is now ' . $booking->status_label . '.');

        return redirect(route('payment-bookings.approvals'));
    }

    /**
     * Reviewers see every booking; everyone else only their own. Returns null and
     * flashes when the booking is missing or out of reach.
     */
    private function findVisible($id)
    {
        $booking = PaymentBooking::with(['creator', 'approvals.user'])->find($id);

        if (!$booking) {
            Flash::error(__('models/payment_bookings.singular') . ' ' . __('messages.not_found'));

            return null;
        }

        if ($this->scopeUserId() !== null && (int) $booking->created_by !== (int) auth()->id()) {
            Flash::error('You can only work with your own payment bookings.');

            return null;
        }

        return $booking;
    }

    /**
     * The user id to scope figures and listings to, or null for a reviewer who
     * legitimately sees the whole ledger.
     */
    private function scopeUserId()
    {
        $user = auth()->user();

        if ($user->can('verify_payment_bookings') || $user->can('approve_payment_bookings')) {
            return null;
        }

        return $user->id;
    }

    private function guardReviewer()
    {
        if (empty(PaymentBookingService::levelsForUser())) {
            abort(403, 'You do not have permission to review payment bookings.');
        }
    }

}
