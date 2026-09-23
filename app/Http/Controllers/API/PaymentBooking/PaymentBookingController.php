<?php

namespace App\Http\Controllers\API\PaymentBooking;

use App\Http\Controllers\Controller;
use App\Models\PaymentBooking;
use App\Models\PaymentBookingApproval;
use App\Services\PaymentBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Payment Bookings API — the surface the Flutter app is built against.
 *
 * Every rule lives in App\Services\PaymentBookingService, the same one the portal
 * uses, so a booking created on a phone is indistinguishable from one typed into
 * the web form.
 *
 * Auth: `auth:api` (token guard). Send the token from /api/v1/foreman/login as
 * `Authorization: Bearer <token>`.
 *
 * Visibility follows the portal: an accountant only ever sees their own bookings;
 * a holder of a review permission sees the whole ledger.
 */
class PaymentBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // ---------------------------------------------------------------------
    // Reference data
    // ---------------------------------------------------------------------

    /**
     * Everything the app needs to render the form and the status filters without
     * hard-coding our enums.
     *
     * GET /api/v1/payment-bookings/meta
     */
    public function meta(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'booking_types' => $this->options(PaymentBooking::types()),
                'statuses'      => $this->options(PaymentBooking::statuses()),
                'decisions'     => $this->options(PaymentBookingApproval::decisions()),
                'levels'        => $this->options(PaymentBookingApproval::levels()),
                'currency'      => 'AED',
                'next_reference' => [
                    'cheque' => PaymentBookingService::nextReference(PaymentBooking::TYPE_CHEQUE),
                    'cash'   => PaymentBookingService::nextReference(PaymentBooking::TYPE_CASH),
                ],
                'permissions'   => [
                    'can_verify'  => (bool) $user->can('verify_payment_bookings'),
                    'can_approve' => (bool) $user->can('approve_payment_bookings'),
                    'sees_all'    => $this->scopeUserId($request) === null,
                ],
            ],
        ], 200);
    }

    // ---------------------------------------------------------------------
    // CRUD
    // ---------------------------------------------------------------------

    /**
     * GET /api/v1/payment-bookings
     *
     * Filters: booking_type, status, date_from, date_to, search, released
     * Paging:  page, limit  |  Sorting: sort_key, sort_direction
     */
    public function index(Request $request)
    {
        try {
            $query = PaymentBooking::with('creator');

            if (($userId = $this->scopeUserId($request)) !== null) {
                $query->ownedBy($userId);
            }

            if ($request->filled('booking_type')) {
                $query->ofType($request->input('booking_type'));
            }

            if ($request->filled('status')) {
                $query->whereIn('status', array_map('intval', (array) $request->input('status')));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('booking_date', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('booking_date', '<=', $request->input('date_to'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('reference_no', 'like', "%{$search}%")
                        ->orWhere('payee', 'like', "%{$search}%")
                        ->orWhere('project_cost_centre', 'like', "%{$search}%")
                        ->orWhere('cheque_number', 'like', "%{$search}%");
                });
            }

            $sortKey       = $request->input('sort_key', 'id');
            $sortDirection = strtolower($request->input('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';
            $allowedSorts  = ['id', 'booking_date', 'amount', 'reference_no', 'release_date', 'payment_date', 'status'];

            $query->orderBy(in_array($sortKey, $allowedSorts, true) ? $sortKey : 'id', $sortDirection);

            $limit = max(1, min((int) $request->input('limit', 15), 100));
            $page  = max(1, (int) $request->input('page', 1));

            $bookings = $query->paginate($limit, ['*'], 'page', $page);

            // `released` filters on a derived value, so it is applied to the page
            // rather than the query. Documented as such for the app.
            $items = collect($bookings->items());

            if ($request->filled('released')) {
                $wantReleased = filter_var($request->input('released'), FILTER_VALIDATE_BOOLEAN);
                $items = $items->filter(function ($booking) use ($wantReleased) {
                    return $booking->is_released === $wantReleased;
                })->values();
            }

            return response()->json([
                'success'    => true,
                'data'       => $items->map(function ($booking) {
                    return $this->transform($booking);
                })->all(),
                'pagination' => [
                    'total'        => $bookings->total(),
                    'per_page'     => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'last_page'    => $bookings->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('index', $e);
        }
    }

    /**
     * GET /api/v1/payment-bookings/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $booking = $this->findVisible($request, $id);

            if (!$booking) {
                return $this->notFound();
            }

            list($canDecide, $reason) = PaymentBookingService::canDecide($booking, $request->user());

            return response()->json([
                'success' => true,
                'data'    => $this->transform($booking, true) + [
                    'can_decide'        => $canDecide,
                    'cannot_decide_why' => $canDecide ? null : $reason,
                    'can_edit'          => $booking->isEditable()
                        && (int) $booking->created_by === (int) $request->user()->id,
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('show', $e);
        }
    }

    /**
     * POST /api/v1/payment-bookings
     *
     * Body: booking_type (cheque|cash) + the payment information block, plus
     * cheque_number/cheque_date/bank_account/release_date for a cheque, or
     * cash_account/payment_date for cash. Pass submit=false to keep it as a draft.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                PaymentBooking::rulesFor($request->input('booking_type'))
            );

            if ($validator->fails()) {
                return $this->validationError($validator);
            }

            $submit = $request->has('submit')
                ? filter_var($request->input('submit'), FILTER_VALIDATE_BOOLEAN)
                : true;

            $booking = PaymentBookingService::store($validator->validated(), $request->user()->id, $submit);

            return response()->json([
                'success' => true,
                'message' => 'Payment booking ' . $booking->reference_no . ' created.',
                'data'    => $this->transform($booking->fresh(['creator', 'approvals.user']), true),
            ], 201);
        } catch (\Exception $e) {
            return $this->fail('store', $e);
        }
    }

    /**
     * PUT /api/v1/payment-bookings/{id}
     *
     * Only the creator, and only while the booking is a draft, pending, or has come
     * back rejected. The booking type is fixed at creation.
     */
    public function update(Request $request, $id)
    {
        try {
            $booking = $this->findVisible($request, $id);

            if (!$booking) {
                return $this->notFound();
            }

            if ((int) $booking->created_by !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only edit your own bookings.',
                ], 403);
            }

            if (!$booking->isEditable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking can no longer be edited — it has already been reviewed.',
                ], 422);
            }

            $rules = PaymentBooking::rulesFor($booking->booking_type);
            unset($rules['booking_type']);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->validationError($validator);
            }

            $booking = PaymentBookingService::update($booking, $validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Payment booking updated.',
                'data'    => $this->transform($booking->load(['creator', 'approvals.user']), true),
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('update', $e);
        }
    }

    /**
     * DELETE /api/v1/payment-bookings/{id}
     *
     * Soft delete. An approved booking is a financial record and is never removed.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $booking = $this->findVisible($request, $id);

            if (!$booking) {
                return $this->notFound();
            }

            if ((int) $booking->created_by !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own bookings.',
                ], 403);
            }

            if ($booking->status === PaymentBooking::STATUS_APPROVED) {
                return response()->json([
                    'success' => false,
                    'message' => 'An approved booking cannot be deleted.',
                ], 422);
            }

            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment booking deleted.',
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('destroy', $e);
        }
    }

    /**
     * POST /api/v1/payment-bookings/{id}/submit
     *
     * Send a draft — or a booking that came back rejected — into the review chain.
     */
    public function submit(Request $request, $id)
    {
        try {
            $booking = $this->findVisible($request, $id);

            if (!$booking) {
                return $this->notFound();
            }

            if ((int) $booking->created_by !== (int) $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only submit your own bookings.',
                ], 403);
            }

            if (!in_array($booking->status, [PaymentBooking::STATUS_DRAFT, PaymentBooking::STATUS_REJECTED], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only a draft or rejected booking can be submitted.',
                ], 422);
            }

            $booking = PaymentBookingService::submit($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking ' . $booking->reference_no . ' submitted for review.',
                'data'    => $this->transform($booking->load(['creator', 'approvals.user']), true),
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('submit', $e);
        }
    }

    // ---------------------------------------------------------------------
    // Review chain
    // ---------------------------------------------------------------------

    /**
     * GET /api/v1/payment-bookings/approvals/queue
     *
     * What this reviewer can act on right now. `?filter=all` widens it to every
     * booking that has entered the chain.
     */
    public function queue(Request $request)
    {
        try {
            $levels = PaymentBookingService::levelsForUser($request->user());

            if (empty($levels)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to review payment bookings.',
                ], 403);
            }

            $query = PaymentBookingService::scopeQueue(
                PaymentBooking::with(['creator', 'approvals.user']),
                $request->user(),
                $request->input('filter') === 'all' ? 'all' : 'pending'
            );

            $limit = max(1, min((int) $request->input('limit', 15), 100));
            $page  = max(1, (int) $request->input('page', 1));

            $bookings = $query->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success'    => true,
                'data'       => collect($bookings->items())->map(function ($booking) use ($request) {
                    list($canDecide, $reason) = PaymentBookingService::canDecide($booking, $request->user());

                    return $this->transform($booking, true) + [
                        'can_decide'        => $canDecide,
                        'cannot_decide_why' => $canDecide ? null : $reason,
                    ];
                })->all(),
                'pagination' => [
                    'total'        => $bookings->total(),
                    'per_page'     => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'last_page'    => $bookings->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('queue', $e);
        }
    }

    /**
     * POST /api/v1/payment-bookings/{id}/approve   (note optional)
     * POST /api/v1/payment-bookings/{id}/reject    (note required)
     * POST /api/v1/payment-bookings/{id}/hold      (note required)
     */
    public function approve(Request $request, $id)
    {
        return $this->decide($request, $id, PaymentBookingApproval::DECISION_APPROVED, false);
    }

    public function reject(Request $request, $id)
    {
        return $this->decide($request, $id, PaymentBookingApproval::DECISION_REJECTED, true);
    }

    public function hold(Request $request, $id)
    {
        return $this->decide($request, $id, PaymentBookingApproval::DECISION_HOLD, true);
    }

    /**
     * POST /api/v1/payment-bookings/{id}/resume
     */
    public function resume(Request $request, $id)
    {
        try {
            if (empty(PaymentBookingService::levelsForUser($request->user()))) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to review payment bookings.',
                ], 403);
            }

            $booking = PaymentBooking::with('approvals')->find($id);

            if (!$booking) {
                return $this->notFound();
            }

            if ($booking->status !== PaymentBooking::STATUS_ON_HOLD) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only a booking on hold can be resumed.',
                ], 422);
            }

            $booking = PaymentBookingService::resume($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking ' . $booking->reference_no . ' is back in the review queue.',
                'data'    => $this->transform($booking->load(['creator', 'approvals.user']), true),
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('resume', $e);
        }
    }

    // ---------------------------------------------------------------------
    // Push notifications
    // ---------------------------------------------------------------------

    /**
     * POST /api/v1/payment-bookings/device-token
     *
     * The app calls this after sign-in and whenever FCM rotates its token.
     * Registering a token that already exists reassigns it to this user rather
     * than duplicating, so a shared handset does not keep notifying the
     * previous owner.
     *
     * Body: { token, platform?, device_name? }
     */
    public function registerDeviceToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token'       => 'required|string|max:512',
                'platform'    => 'nullable|string|max:20',
                'device_name' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator);
            }

            \App\Models\DeviceToken::register(
                $request->user()->id,
                $request->input('token'),
                $request->input('platform', 'android'),
                $request->input('device_name')
            );

            return response()->json([
                'success' => true,
                'message' => 'Device registered for notifications.',
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('registerDeviceToken', $e);
        }
    }

    /**
     * DELETE /api/v1/payment-bookings/device-token
     *
     * Called on sign-out so a shared or handed-on phone stops receiving this
     * user's notifications.
     */
    public function deleteDeviceToken(Request $request)
    {
        try {
            $token = $request->input('token');

            $query = \App\Models\DeviceToken::where('user_id', $request->user()->id);
            if ($token) {
                $query->where('token', $token);
            }
            $removed = $query->delete();

            return response()->json([
                'success' => true,
                'message' => 'Device unregistered.',
                'removed' => $removed,
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('deleteDeviceToken', $e);
        }
    }

    // ---------------------------------------------------------------------
    // Money
    // ---------------------------------------------------------------------

    /**
     * GET /api/v1/payment-bookings/summary
     *
     * The home-screen figures: released this month, released next month, what is
     * approved but not yet out the door, and what is still in review.
     * `?months=6` also returns the release schedule bucketed by month.
     */
    public function summary(Request $request)
    {
        try {
            $userId = $this->scopeUserId($request);
            $months = (int) $request->input('months', 6);
            $months = max(3, min($months, 12));

            // A custom range wins over `months`, matching the portal dashboard.
            // Expected as YYYY-MM; anything unparseable is ignored rather than
            // erroring, so a bad query string degrades to the default window.
            $from = $this->monthOrNull($request->input('from'));
            $to   = $this->monthOrNull($request->input('to'));
            if ($from === null || $to === null) {
                $from = $to = null;
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'currency'     => 'AED',
                    'scoped_to_me' => $userId !== null,
                    'summary'      => PaymentBookingService::summary($userId),
                    'schedule'     => PaymentBookingService::releaseSchedule(
                        $userId, $months, null, $from, $to
                    ),
                    'range'        => ['from' => $from, 'to' => $to, 'months' => $months],
                    // The next approved payments whose money is due to move.
                    'upcoming'     => PaymentBookingService::upcomingReleases($userId)
                        ->map(function ($b) {
                            return $this->transform($b);
                        })->values(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('summary', $e);
        }
    }

    /**
     * Accepts YYYY-MM and returns it, or null when absent/malformed.
     */
    private function monthOrNull($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return preg_match('/^\d{4}-\d{2}$/', (string) $value) ? (string) $value : null;
    }

    // ---------------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------------

    private function decide(Request $request, $id, $decision, $noteRequired)
    {
        try {
            if (empty(PaymentBookingService::levelsForUser($request->user()))) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to review payment bookings.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'note' => ($noteRequired ? 'required' : 'nullable') . '|string|max:2000',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator);
            }

            $booking = PaymentBooking::with('approvals')->find($id);

            if (!$booking) {
                return $this->notFound();
            }

            list($allowed, $reason) = PaymentBookingService::canDecide($booking, $request->user());

            if (!$allowed) {
                return response()->json(['success' => false, 'message' => $reason], 403);
            }

            $booking = PaymentBookingService::decide(
                $booking,
                $request->user()->id,
                $decision,
                $request->input('note')
            );

            return response()->json([
                'success' => true,
                'message' => 'Decision saved. ' . $booking->reference_no . ' is now ' . $booking->status_label . '.',
                'data'    => $this->transform($booking->load(['creator', 'approvals.user']), true),
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('decide', $e);
        }
    }

    /**
     * One booking as JSON. `$withChain` adds the two-level review detail.
     */
    private function transform(PaymentBooking $booking, $withChain = false)
    {
        $payload = [
            'id'                  => (int) $booking->id,
            'reference_no'        => $booking->reference_no,
            'booking_type'        => $booking->booking_type,
            'booking_type_label'  => $booking->type_label,

            'payee'               => $booking->payee,
            'payment_against'     => $booking->payment_against,
            'purpose'             => $booking->purpose,
            'amount'              => (float) $booking->amount,
            'currency'            => 'AED',
            'project_cost_centre' => $booking->project_cost_centre,
            'booking_date'        => $this->date($booking->booking_date),

            'cheque_number'       => $booking->cheque_number,
            'cheque_date'         => $this->date($booking->cheque_date),
            'bank_account'        => $booking->bank_account,
            'release_date'        => $this->date($booking->release_date),

            'cash_account'        => $booking->cash_account,
            'payment_date'        => $this->date($booking->payment_date),

            'status'              => (int) $booking->status,
            'status_label'        => $booking->status_label,
            'effective_date'      => $this->date($booking->effective_date),
            'is_released'         => $booking->is_released,
            'released_amount'     => $booking->released_amount,

            'created_by'          => $booking->created_by ? (int) $booking->created_by : null,
            'created_by_name'     => $booking->creator ? $booking->creator->name : null,
            'submitted_at'        => $booking->submitted_at ? $booking->submitted_at->toDateTimeString() : null,
            'verified_at'         => $booking->verified_at ? $booking->verified_at->toDateTimeString() : null,
            'approved_at'         => $booking->approved_at ? $booking->approved_at->toDateTimeString() : null,
            'rejected_at'         => $booking->rejected_at ? $booking->rejected_at->toDateTimeString() : null,
            'created_at'          => $booking->created_at ? $booking->created_at->toDateTimeString() : null,
            'updated_at'          => $booking->updated_at ? $booking->updated_at->toDateTimeString() : null,
        ];

        if ($withChain) {
            $payload['approval_chain'] = PaymentBookingService::chain($booking);
        }

        return $payload;
    }

    private function date($value)
    {
        return $value ? $value->format('Y-m-d') : null;
    }

    /**
     * Turn a [key => label] enum into the list shape the app binds dropdowns to.
     */
    private function options(array $map)
    {
        $out = [];

        foreach ($map as $value => $label) {
            $out[] = ['value' => $value, 'label' => $label];
        }

        return $out;
    }

    /**
     * A reviewer sees everything; everyone else only their own bookings.
     */
    private function scopeUserId(Request $request)
    {
        $user = $request->user();

        if ($user->can('verify_payment_bookings') || $user->can('approve_payment_bookings')) {
            return null;
        }

        return $user->id;
    }

    private function findVisible(Request $request, $id)
    {
        $booking = PaymentBooking::with(['creator', 'approvals.user'])->find($id);

        if (!$booking) {
            return null;
        }

        $scoped = $this->scopeUserId($request);

        if ($scoped !== null && (int) $booking->created_by !== (int) $scoped) {
            return null;
        }

        return $booking;
    }

    private function notFound()
    {
        return response()->json([
            'success' => false,
            'message' => 'Payment booking not found.',
        ], 404);
    }

    private function validationError($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    private function fail($action, \Exception $e)
    {
        Log::error('PaymentBookingController@' . $action . ' error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while processing the request.',
        ], 500);
    }
}
