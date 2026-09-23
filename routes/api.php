<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\ForemanAuthController;
use App\Http\Controllers\API\Attendance\ForemanAttendanceController;
use App\Http\Controllers\API\Attendance\ManagerAttendanceController;
use App\Http\Controllers\API\Attendance\QRAttendanceController;
use App\Http\Controllers\API\Attendance\PwaQrAttendanceController;
use App\Http\Controllers\API\Site\SiteController;
use App\Http\Controllers\API\PaymentBooking\PaymentBookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'UserController@login');

Route::group(['middleware' => ['auth:api']], function() {
    //update user firebase token
    Route::post('/user/update_firebase_token', 'UserController@update_firebase_token');

    // form requests
    Route::get('/get_request_forms', 'RequestController@get_request_forms');
    Route::get('/get_updated_request_forms', 'RequestController@get_updated_request_forms');
    Route::post('/update_request_status', 'RequestController@update_request_status');

        // To get all receipts or filtered by get paramenter
        Route::get('/receipts','ReceiptController@index');

        // To get all payments  or filtered by get paramenter
        Route::get('/payments','PaymentController@index');

        // To get all cheques  or filtered by get paramenter
        Route::get('/cheques','ChequesController@index');
});


// ============================================
// API V1 ROUTES - ATTENDANCE
// ============================================

Route::  prefix('v1')->group(function () {

    // ============================================
    // PUBLIC AUTH ENDPOINTS
    // ============================================

    Route::post('/foreman/login', [ForemanAuthController::class, 'login']);

    // ============================================
    // PROTECTED AUTH ENDPOINTS - Using api guard
    // ============================================

    Route::middleware('auth:api')->group(function () {
        Route::post('/foreman/logout', [ForemanAuthController:: class, 'logout']);
        Route::post('/foreman/refresh-token', [ForemanAuthController::class, 'refreshToken']);

        // ============================================
        // FOREMAN ATTENDANCE ENDPOINTS
        // ============================================

        Route::prefix('attendance')->group(function () {
            Route::get('/today', [ForemanAttendanceController::class, 'getTodayLabors']);
            Route::post('/submit', [ForemanAttendanceController::class, 'submitAttendance']);
            Route::get('/check-submitted', [ForemanAttendanceController::class, 'checkSubmitted']);
            Route::get('/history', [ForemanAttendanceController::class, 'getHistory']);
            Route::get('/export', [ForemanAttendanceController::class, 'exportAttendance']);
        });

        // ============================================
        // MANAGER ATTENDANCE ENDPOINTS
        // ============================================

        Route:: prefix('attendance')->group(function () {
            Route::get('/pending', [ManagerAttendanceController::class, 'getPending']);
            Route::post('/{attendance}/approve', [ManagerAttendanceController::class, 'approve']);
            Route::post('/{attendance}/reject', [ManagerAttendanceController::class, 'reject']);
            Route::get('/approved', [ManagerAttendanceController::class, 'getApproved']);
        });

        Route::prefix('sites')->group(function () {
            Route::get('/', [SiteController::class, 'index']);           // GET /api/v1/sites
            Route::post('/', [SiteController::class, 'store']);          // POST /api/v1/sites
            Route::get('/{site}', [SiteController::class, 'show']);      // GET /api/v1/sites/{id}
            Route:: put('/{site}', [SiteController::class, 'update']);    // PUT /api/v1/sites/{id}
            Route:: delete('/{site}', [SiteController::class, 'destroy']); // DELETE /api/v1/sites/{id}
        });

        // ============================================
        // PWA QR STAFF ATTENDANCE ENDPOINTS
        // ============================================
        Route::prefix('pwa-qr-attendance')->group(function () {
            Route::post('/scan', [PwaQrAttendanceController::class, 'scan']);
            Route::get('/lookup-staff/{staffId}', [PwaQrAttendanceController::class, 'lookupStaff']);
            Route::get('/today', [PwaQrAttendanceController::class, 'todayRecords']);
            Route::get('/sites', [PwaQrAttendanceController::class, 'getSites']);
        });

        // ============================================
        // QR + GEOLOCATION ATTENDANCE ENDPOINTS
        // ============================================
        Route::prefix('qr-attendance')->group(function () {
            Route::post('/scan', [QRAttendanceController::class, 'scanQR']);                          // POST /api/v1/qr-attendance/scan
            Route::get('/today', [QRAttendanceController::class, 'getTodayAttendance']);              // GET /api/v1/qr-attendance/today
            Route::get('/summary', [QRAttendanceController::class, 'getAttendanceSummary']);          // GET /api/v1/qr-attendance/summary
            Route::get('/open-session', [QRAttendanceController::class, 'getOpenSession']);           // GET /api/v1/qr-attendance/open-session
            Route::post('/evaluate-day', [QRAttendanceController::class, 'evaluateDay']);             // POST /api/v1/qr-attendance/evaluate-day
            Route::get('/shift-rules', [QRAttendanceController::class, 'getShiftRules']);             // GET /api/v1/qr-attendance/shift-rules
        });
    });

});


// ============================================
// PRESENT ATTENDANCE MODULE
// ============================================
Route::prefix('v1/present-attendance')->middleware('auth:api')->group(function () {
    // Fetch labors and site details for today
    Route::get('/today', [App\Http\Controllers\API\Attendance\PresentAttendanceController::class, 'getTodayLabors']);

    // Submit attendance with site and overtime
    Route::post('/submit', [App\Http\Controllers\API\Attendance\PresentAttendanceController::class, 'submitAttendance']);
});

// ============================================
// PRESENT ATTENDANCE ENDPOINT
// ============================================

Route::prefix('v1/present-attendance')->middleware('auth:api')->group(function () {
    Route::get('/history', [App\Http\Controllers\API\Attendance\PresentAttendanceController::class, 'getHistory']);
});


// ============================================
// PAYMENT BOOKING MODULE
// ============================================
//
// Cheque and cash payment bookings with a sequential two-level review, for the
// Flutter app. Auth is the `api` token guard — send the token returned by
// /api/v1/foreman/login as `Authorization: Bearer <token>`.
//
// Visibility mirrors the portal: an accountant sees only their own bookings, a
// holder of verify_payment_bookings / approve_payment_bookings sees all of them.
//
//   GET    /api/v1/payment-bookings/meta          form + filter reference data
//   GET    /api/v1/payment-bookings/summary       released this/next month, schedule
//   GET    /api/v1/payment-bookings/approvals     the reviewer's queue
//   GET    /api/v1/payment-bookings               list (filter, search, paginate)
//   POST   /api/v1/payment-bookings               create (submit=false for a draft)
//   GET    /api/v1/payment-bookings/{id}          detail + approval chain
//   PUT    /api/v1/payment-bookings/{id}          edit (creator, before review)
//   DELETE /api/v1/payment-bookings/{id}          soft delete (never once approved)
//   POST   /api/v1/payment-bookings/{id}/submit   draft/rejected -> review queue
//   POST   /api/v1/payment-bookings/{id}/approve  record this level's approval
//   POST   /api/v1/payment-bookings/{id}/reject   note required
//   POST   /api/v1/payment-bookings/{id}/hold     note required
//   POST   /api/v1/payment-bookings/{id}/resume   held -> back in the queue
//
Route::prefix('v1/payment-bookings')->middleware('auth:api')->group(function () {
    // Static segments first, so they are never swallowed by /{id}.
    Route::get('/meta', [PaymentBookingController::class, 'meta']);

    // Push notification device registration.
    Route::post('/device-token', [PaymentBookingController::class, 'registerDeviceToken']);
    Route::delete('/device-token', [PaymentBookingController::class, 'deleteDeviceToken']);
    Route::get('/summary', [PaymentBookingController::class, 'summary']);
    Route::get('/approvals', [PaymentBookingController::class, 'queue']);

    Route::get('/', [PaymentBookingController::class, 'index']);
    Route::post('/', [PaymentBookingController::class, 'store']);

    Route::get('/{id}', [PaymentBookingController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/{id}', [PaymentBookingController::class, 'update'])->where('id', '[0-9]+');
    Route::patch('/{id}', [PaymentBookingController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/{id}', [PaymentBookingController::class, 'destroy'])->where('id', '[0-9]+');

    Route::post('/{id}/submit', [PaymentBookingController::class, 'submit'])->where('id', '[0-9]+');
    Route::post('/{id}/approve', [PaymentBookingController::class, 'approve'])->where('id', '[0-9]+');
    Route::post('/{id}/reject', [PaymentBookingController::class, 'reject'])->where('id', '[0-9]+');
    Route::post('/{id}/hold', [PaymentBookingController::class, 'hold'])->where('id', '[0-9]+');
    Route::post('/{id}/resume', [PaymentBookingController::class, 'resume'])->where('id', '[0-9]+');
});
