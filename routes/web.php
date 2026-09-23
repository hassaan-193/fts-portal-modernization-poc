<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.These
| routes are loaded by the RouteServiceProvider within a group which  
| contains the "web" middleware group.Now create something great!
|
*/

use App\Http\Controllers\TicketController;
use App\Http\Controllers\LaborController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QrStaffAttendanceAdminController;

//pipeline check here 

Route::resource('orders','OrderController')->middleware(['auth']);

Route::prefix('projects')->middleware('auth')->group(function () {
    // Visit report form
    Route::get('visit-form', 'ProjectReportController@showForm')->name('projects.showForm');
    Route::post('submit-report', 'ProjectReportController@store')->name('projects.submitReport');

    // Draft reports: saved from the visit form and finished later, private to their author.
    // Delete is a POST on purpose: CheckDeletePermission only lets holders of the global
    // `deletes` permission send DELETE requests, and discarding your own draft shouldn't need that.
    Route::get('amc-drafts', 'ProjectReportController@drafts')->name('projects.drafts');
    Route::post('delete-draft/{id}', 'ProjectReportController@destroyDraft')->name('projects.deleteDraft');

    // Dropdown data for report form
    Route::get('get-reference-number', 'ProjectReportController@generateReferenceNumber')->name('projects.getReferenceNumber');
    Route::get('get-clients', 'ProjectReportController@getClients')->name('projects.getClients');
    Route::get('get-projects', 'ProjectReportController@getProjects')->name('projects.getProjects');
    Route::get('get-visit-schedules', 'ProjectReportController@getVisitSchedules')->name('projects.getVisitSchedules');
    Route::get('get-projects-by-company', 'ProjectReportController@getProjectsByCompany')->name('projects.getProjectsByCompany');

    // Admin project report management
    Route::get('report-status', 'ProjectReportController@showReportStatusPage')->name('projects.reportStatus');
    Route::post('update-report-status/{id}', 'ProjectReportController@updateReportStatus')->name('projects.updateReportStatus');

    // View/edit/update reports
    Route::get('view-report/{id}', 'ProjectReportController@viewReport')->name('projects.viewReport');
    Route::get('edit-report/{id}', 'ProjectReportController@editReport')->name('projects.editReport');
    Route::post('update-report/{id}', 'ProjectReportController@updateReport')->name('projects.updateReport');

    // AMC project tracking
    Route::get('visit-tracking', 'ProjectController@visitTracking')->name('projects.visit-tracking');
});

Route::prefix('visit-schedules')->name('visit-schedules.')->middleware('auth')->group(function () {

    Route::post('{id}/upload-file', 'VisitScheduleController@uploadFile')->name('upload-file');
    Route::patch('{id}/update-status', 'VisitScheduleController@updateStatus')->name('update-status');

    // Comments
    Route::get('{id}/comments', 'VisitScheduleController@showComments')->name('comments');
    Route::post('{id}/comments', 'VisitScheduleController@storeComment')->name('store-comment');
    Route::delete('{visitScheduleId}/comments/{commentId}', 'VisitScheduleController@deleteComment')->name('delete-comment');
    Route::get('{visitScheduleId}/comments/{commentId}/edit', 'VisitScheduleController@editComment')->name('edit-comment');
    Route::put('{visitScheduleId}/comments/{commentId}', 'VisitScheduleController@updateComment')->name('update-comment');

    // History
    Route::get('history', 'VisitScheduleController@showHistory')->name('history');
    Route::get('visit-schedules/history-data', 'VisitScheduleController@getHistoryData')->name('visit-schedules.history.data');
});
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email Verification Routes...
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify'); // v6.x
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// Company Authentication Routes...
Route::prefix('company')->group(function() {
    Route::get('/login', 'Auth\CompanyLoginController@showLoginForm')->name('company.login');
    Route::post('/login', 'Auth\CompanyLoginController@login')->name('company.login.submit');
    Route::post('/logout', 'Auth\CompanyLoginController@logout')->name('company.logout');

    // Company Password Reset Routes...
    Route::get('/password/reset', function() { return redirect('/login'); })->name('company.password.request');
    Route::post('/password/email', function() { return redirect('/login'); })->name('company.password.email');
    Route::get('/password/reset/{token}', function() { return redirect('/login'); })->name('company.password.reset');
    Route::post('/password/reset', function() { return redirect('/login'); })->name('company.password.update');
});

Route::prefix('company')->middleware('auth:company')->group(function() {
    Route::get('/dashboard', 'CompanyHomeController@index')->name('company.dashboard');
    Route::get('/{id}/profile', 'CompanyHomeController@user_profile')->name('company.user_profile');
    Route::put('/{id}/profile', 'CompanyHomeController@update_user_profile')->name('company.update_user_profile');

});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/', 'HomeController@index')->name('home');

    // Notification
    Route::post('notifications/NotifMarkAsRead', 'NotificationController@MarkAsRead')->name('notifications.MarkAsRead');

    // User And  Roles Routes...
    Route::resource('roles','RoleController')->middleware(['can:roles']);
    Route::resource('users', 'UserController')->middleware(['can:users']);
    Route::group([ 'prefix' => 'users'], function () {
        Route::get('/{id}/profile', 'UserController@user_profile')->name('users.user_profile');
        Route::put('/{id}/profile', 'UserController@update_user_profile')->name('users.update_user_profile');
    });

    Route::resource('quotations', 'QuotationController');
    Route::group([ 'prefix' => 'quotations'], function () {
        Route::get('/status/{id}/update', 'QuotationController@update_status')->name('quotations.update_status');
        Route::post('/add/type/ajax', 'QuotationController@add_type_ajax')->name('quotations.add_type_ajax');
    });
    Route::get('/get-amc-projects', 'QuotationController@getAmcProjects')->name('get-amc-projects');
    Route::resource('lpoins', 'LpoinController');
    Route::resource('drawing-receiveds', 'DrawingReceivedController');
    Route::group(['prefix' => 'drawing-receiveds'], function () {
        Route::get('engineer/dashboard', 'DrawingReceivedController@engineerDashboard')->name('drawing-receiveds.engineer-dashboard');
        Route::get('{id}/contribute', 'DrawingReceivedController@contributeForm')->name('drawing-receiveds.contribute-form');
        Route::post('{id}/store-contribution', 'DrawingReceivedController@storeContribution')->name('drawing-receiveds.store-contribution');
        Route::get('{id}/contributions', 'DrawingReceivedController@contributionHistory')->name('drawing-receiveds.contributions');
    });
    Route::resource('vendors', 'VendorController');
    Route::group([ 'prefix' => 'vendors'], function () {
        Route::post('/add/ajax', 'VendorController@add_vendor_ajax')->name('vendors.add_vendor_ajax');
        Route::get('{vendorId}/terms-and-conditions', 'VendorController@getTermsAndConditions')->name('vendors.terms-and-conditions');
    });
    Route::get('lpoouts/{lpoout}/print', 'LpooutController@print')->name('lpoouts.print');
    Route::get('lpoouts/{lpoout}/download-pdf', 'LpooutController@downloadPdf')->name('lpoouts.downloadPdf');
    Route::get('/vendor/{id}/vat', 'LpooutController@getVat');
    Route::get('/vendor/{id}/payment-preference', 'LpooutController@getVendorPaymentPreference');
    Route::resource('lpoouts', 'LpooutController');
    Route::get('lpoouts/status/manage', 'LpooutController@manageStatus')->name('lpoouts.manageStatus');
    Route::post('lpoouts/{id}/approve', 'LpooutController@approve')->name('lpoouts.approve');
    Route::post('lpoouts/{id}/disapprove', 'LpooutController@disapprove')->name('lpoouts.disapprove');
    // LPO Revision: redirects into the Purchase Order create form, pre-filled, to restart the approval workflow
    Route::get('lpoouts/{id}/revise', 'LpooutController@showRevise')->name('lpoouts.showRevise');

    // Vendor Payables (pending obligations without LPO workflow)
    Route::resource('vendor-payables', 'VendorPayableController')->only(['index','create','store','show','edit','update','destroy']);
    Route::patch('vendor-payables/{id}/cancel', 'VendorPayableController@cancel')->name('vendor-payables.cancel');

    // Payment Analytics Dashboard
    Route::get('payment-analytics', 'PaymentAnalyticsController@index')->name('payment-analytics.index');

    // Purchase Orders Routes
    Route::resource('purchase-orders', 'PurchaseOrderController');
    Route::get('purchase-orders-department/index', 'PurchaseOrderController@departmentIndex')->name('purchase-orders.departmentIndex');
    Route::get('purchase-orders-department/{id}/edit', 'PurchaseOrderController@departmentEdit')->name('purchase-orders.departmentEdit');
    Route::post('purchase-orders-department/{id}/update', 'PurchaseOrderController@departmentUpdate')->name('purchase-orders.departmentUpdate');
    Route::post('purchase-orders-department/{id}/reject', 'PurchaseOrderController@departmentReject')->name('purchase-orders.departmentReject');
    Route::post('purchase-orders-department/{id}/send-back', 'PurchaseOrderController@departmentSendBack')->name('purchase-orders.departmentSendBack');
    Route::get('purchase-orders-admin/index', 'PurchaseOrderController@adminIndex')->name('purchase-orders.adminIndex');
    Route::get('purchase-orders-admin/{id}/show', 'PurchaseOrderController@adminShow')->name('purchase-orders.adminShow');
    Route::post('purchase-orders-admin/{id}/approve', 'PurchaseOrderController@adminApprove')->name('purchase-orders.adminApprove');
    Route::post('purchase-orders-admin/{id}/approve-with-email', 'PurchaseOrderController@adminApproveWithEmail')->name('purchase-orders.adminApproveWithEmail');
    Route::post('purchase-orders-admin/{id}/reject', 'PurchaseOrderController@adminReject')->name('purchase-orders.adminReject');
    Route::post('purchase-orders-admin/{id}/hold', 'PurchaseOrderController@adminHold')->name('purchase-orders.adminHold');

    Route::group([ 'prefix' => 'media'], function () {
        Route::get('/{id}/delete', 'MediaController@delete_file')->name('media.delete_file');
    });
    Route::get('projects/visit-tracking-export-excel', [App\Http\Controllers\ProjectController::class, 'visitTrackingExportExcel'])->name('projects.visit-tracking-export-excel');
    Route::get('/projects/visit-tracking-export', [App\Http\Controllers\ProjectController::class, 'visitTrackingExport'])->name('projects.visit-tracking-export');
    Route::resource('projects', 'ProjectController');
    Route::group([ 'prefix' => 'projects'], function () {
        Route::get('/{id}/extensions', 'ProjectController@get_extensions')->name('projects.get_extensions');
        Route::post('/comment', 'ProjectController@store_comment')->name('projects.store_comment');
        Route::get('/view/{id}/extensions', 'ProjectController@view_extensions')->name('projects.view_extensions');
        Route::post('/{id}/extensions', 'ProjectController@store_extensions')->name('projects.store_extensions');
        Route::delete('/{id}/extensions', 'ProjectController@delete_extensions')->name('projects.delete_extensions');

        Route::get('/{id}/lpoout', 'ProjectController@get_lpoout')->name('projects.get_lpoout');
        Route::post('/{id}/lpoout', 'ProjectController@store_lpoout')->name('projects.store_lpoout');
        Route::delete('/{id}/lpoout', 'ProjectController@delete_lpoout')->name('projects.delete_lpoout');

    });

    Route::resource('companies', 'CompanyController');
    Route::group([ 'prefix' => 'companies'], function () {
        Route::post('/add/ajax', 'CompanyController@add_company_ajax')->name('companies.add_company_ajax');
        Route::get('/client/{id}/report', 'CompanyController@get_company_report')->name('companies.get_company_report');
        Route::get('/client/{id}/report/export/{type}','CompanyController@export_company_report')->name('companies.export_company_report');
    });

    Route::resource('invoices', 'InvoiceController', ['only' => ['index','store', 'show', 'update', 'edit', 'destroy']]);
    Route::group([ 'prefix' => 'invoices'], function () {
        Route::get('/{request}/create/', 'InvoiceController@create')->name('invoices.create');
        Route::get('/request/invoices', 'InvoiceController@request_invoices')->name('invoices.request_invoices');
        Route::get('/{request_id}/approve-email-form', 'InvoiceController@showApproveEmailForm')->name('invoices.approve-email-form');
        Route::post('/approve-and-email', 'InvoiceController@approveAndEmail')->name('invoices.approve-and-email');
        Route::post('/{id}/send-email', 'InvoiceController@sendInvoiceEmail')->name('invoices.send-email');
    });
    Route::get('invoice-requests/quotation-details/{quotationId}', 'InvoiceRequestController@getQuotationDetails')->name('invoiceRequests.quotationDetails');
    Route::group([ 'prefix' => 'invoiceRequests'], function () {
        Route::get('/lpoout/{lpoout_id}/create', 'InvoiceRequestController@create_with_lpoout')->name('invoiceRequests.create_with_lpoout');
        Route::post('/lpoout/store', 'InvoiceRequestController@store_for_lpoout')->name('invoiceRequests.store_lpoout');
        Route::get('/{lpoin_id}/create/', 'InvoiceRequestController@create_with_lpoin')->name('invoiceRequests.create_with_lpoin');
        Route::get('/get/{quotation}/invoices/', 'InvoiceRequestController@get_quotation_invoices')->name('invoiceRequests.get_quotation_invoices');
        Route::get('/get/{quotation}/invoice-requests/', 'InvoiceRequestController@get_quotation_invoice_requests')->name('invoiceRequests.get_quotation_invoice_requests');
        Route::get('/get/{quotation}/invoice-payments/', 'InvoiceRequestController@get_quotation_invoice_payments')->name('invoiceRequests.get_quotation_invoice_payments');
    });
    Route::resource('invoiceRequests', 'InvoiceRequestController');

    Route::get('/amc-invoices',  'AmcInvoiceController@index')->name('amc.invoices.index');


    Route::resource('laborRequests', 'LaborRequestController')->middleware(['can:laborRequests']);
    Route::group([ 'prefix' => 'laborRequests'], function () {
        Route::post('/{request_id}/update_status', 'LaborRequestController@update_status')->name('laborRequests.update_status');
    });
    Route::resource('paymentInvoices', 'PaymentInvoiceController');
    Route::group([ 'prefix' => 'paymentInvoices'], function () {
        Route::get('/create/invoice/{request}', 'PaymentInvoiceController@create_request_invoice')->name('paymentInvoices.create_invoice');
        Route::get('/create/lpoout/{lpoout}', 'PaymentInvoiceController@createFromLpoout')->name('paymentInvoices.createFromLpoout');
        Route::post('/lpoout/store', 'PaymentInvoiceController@storeFromLpoout')->name('paymentInvoices.storeFromLpoout');
        Route::get('/request/invoices', 'PaymentInvoiceController@request_invoices')->name('paymentInvoices.request_invoices');
    });

    // Accounts routes
    Route::resource('accounts', 'AccountController');

    Route::resource('receipts', 'ReceiptController');
    Route::group([ 'prefix' => 'receipts'], function () {
        Route::post('/company_invoices', 'ReceiptController@company_invoices')->name('receipts.company_invoices');
    });
    Route::resource('payments', 'PaymentController');
    Route::group([ 'prefix' => 'payments'], function () {
        Route::post('/vendor_invoices', 'PaymentController@vendor_invoices')->name('receipts.vendor_invoices');
    });
    Route::resource('cheques', 'ChequeController');
    Route::resource('requestForms', 'RequestFormController');

    Route::resource('pettyCashes', 'PettyCashController');
    Route::resource('pettyCashExpenses', 'PettyCashExpenseController');
    Route::resource('task', 'TaskController');

    /*
     * Payment Bookings — cheque and cash payment bookings with a sequential
     * two-level review.
     *
     * Access is checked in the controller rather than by route middleware: a
     * reviewer holds verify_/approve_payment_bookings but not necessarily
     * `paymentBookings`, and still has to be able to open a booking to decide it.
     * PaymentBookingController's constructor allows either, then narrows per action.
     */
    Route::group(['prefix' => 'payment-bookings'], function () {
        Route::get('/dashboard', 'PaymentBookingController@dashboard')->name('payment-bookings.dashboard');
        Route::get('/approvals', 'PaymentBookingController@approvals')->name('payment-bookings.approvals');
        Route::get('/{id}/submit', 'PaymentBookingController@submit')->name('payment-bookings.submit');
        Route::get('/{id}/resume', 'PaymentBookingController@resume')->name('payment-bookings.resume');
        Route::post('/{id}/approve', 'PaymentBookingController@approve')->name('payment-bookings.approve');
        Route::post('/{id}/reject', 'PaymentBookingController@reject')->name('payment-bookings.reject');
        Route::post('/{id}/hold', 'PaymentBookingController@hold')->name('payment-bookings.hold');
    });
    Route::resource('payment-bookings', 'PaymentBookingController');

    //reports
    Route::group([ 'prefix' => 'reports'], function () {
        Route::get('/pettycash/users', 'ReportController@petty_cash_users')->name('reports.petty_cash_users');
        Route::get('/pettycash/register', 'ReportController@petty_cash_register')->name('reports.petty_cash_register');
        Route::get('/payment', 'ReportController@payment')->name('reports.payment');
        Route::get('/receipt', 'ReportController@receipt')->name('reports.receipt');
        Route::get('/invoice', 'ReportController@invoice')->name('reports.invoice');
        Route::get('/account', 'ReportController@account')->name('reports.account');
        Route::get('/trial-balance', 'ReportController@trial_balance')->name('reports.trial_balance');
    });

    Route::resource('employees', 'EmployeeController')->middleware(['can:employees']);
    
    Route::resource('staf', 'StafProfileController')->middleware(['can:stafprofile']);
    Route::group([ 'prefix' => 'staf'], function () {
        Route::get('document/{id}', 'StafProfileController@document')->name('staf_document');
        Route::get('date/{id}', 'StafProfileController@date')->name('staf_date');
        Route::delete('date/{id}', 'StafProfileController@date_delete')->name('staf_date_destroy');


        // Staff request form...
        Route::get('show_request_form/{id}', 'StafProfileController@show_staff_request_form')->name('show_staff_request_form');
        Route::post('show_request_form', 'StafProfileController@staff_request_form')->name('staff_request_form');
        Route::post('/request/{request_id}/update_status', 'StafProfileController@update_status')->name('staffRequests.update_status');
        Route::delete('request/{id}', 'StafProfileController@destroy_staff_request')->name('staf_request_destroy');
    });

    // Staff Dates CRUD routes
    Route::resource('staf-dates', 'StafDateController')->middleware(['can:stafprofile']);

    Route::get('staffRequests', 'StafProfileController@staff_requests')->name('staff_requests');

    // Self-service request form, isolated from StafProfileController's `can:stafprofile` gate.
    // Authorization is done inside StaffOwnRequestController so a "Staff Requester" login
    // (own_request_form permission only) can reach this without access to the rest of /staf.
    Route::group(['prefix' => 'own-staff-request'], function () {
        Route::get('/', 'StaffOwnRequestController@index')->name('own_requests.index');
        Route::get('create', 'StaffOwnRequestController@create')->name('own_requests.create');
        Route::post('/', 'StaffOwnRequestController@store')->name('own_requests.store');
    });

    // Two-person approval workspace for Staff/Labor requests. Gated inside the
    // controller on the approve_staff_requests / approve_labor_requests permissions,
    // since a user may hold either one, both, or neither.
    Route::group(['prefix' => 'request-approvals'], function () {
        Route::get('/', 'RequestApprovalController@index')->name('request_approvals.index');
        Route::get('{type}/{id}', 'RequestApprovalController@show')
            ->where('type', 'staff|labor')->name('request_approvals.show');
        Route::post('{type}/{id}/approve', 'RequestApprovalController@approve')
            ->where('type', 'staff|labor')->name('request_approvals.approve');
        Route::post('{type}/{id}/disapprove', 'RequestApprovalController@disapprove')
            ->where('type', 'staff|labor')->name('request_approvals.disapprove');
    });

    Route::resource('document', 'DocumentController')->middleware(['can:document']);

    Route::resource('staffPayrolls', 'StaffPayrollController');
    Route::group([ 'prefix' => 'staffPayrolls'], function () {
        Route::get('members/{type}/list/{date}', 'StaffPayrollController@get_members_list')->name('staffPayrolls.get_members_list');
        Route::get('{id}/export-pdf', 'StaffPayrollController@exportPdf')->name('staffPayrolls.exportPdf');
    });

    // ─── Inquiry Management System ────────────────────────────────────────────
    Route::resource('inquiries', 'InquiryController');

    Route::prefix('inquiries')->name('inquiries.')->group(function () {
        // Role-specific dashboards
        Route::get('engineer/my-visits', 'InquiryEngineerController@indexMyVisits')->name('engineer.index');
        Route::get('department/my-inquiries', 'InquiryDepartmentController@indexMyInquiries')->name('department.index');
        Route::get('sales/pipeline', 'InquirySalesController@indexPipeline')->name('sales.index');

        // Department review & assignment
        Route::get('{inquiry}/department/review',    'InquiryDepartmentController@create')->name('department.create');
        Route::post('{inquiry}/department/review',   'InquiryDepartmentController@store')->name('department.store');
        Route::post('{inquiry}/department/forward-sales', 'InquiryDepartmentController@forwardToSales')->name('department.forward-sales');

        // Department – quotation (moved from sales)
        Route::get('{inquiry}/department/quotation',  'InquiryDepartmentController@createQuotation')->name('department.create-quotation');
        Route::post('{inquiry}/department/quotation', 'InquiryDepartmentController@storeQuotation')->name('department.quotation.store');

        // Engineer report
        Route::get('{inquiry}/engineer/report',  'InquiryEngineerController@create')->name('engineer.create');
        Route::post('{inquiry}/engineer/report', 'InquiryEngineerController@store')->name('engineer.store');

        // Sales – follow-ups
        Route::post('{inquiry}/sales/follow-up', 'InquirySalesController@storeFollowUp')->name('sales.follow-up.store');

        // Sales – close
        Route::post('{inquiry}/sales/close', 'InquirySalesController@close')->name('sales.close');
    });
});
Route::get('/staf-profile/expiry-list', [App\Http\Controllers\StafProfileController::class, 'expiryList'])->name('staf-profile.expiry-list');



Route::resource('products', 'ProductController');

// Labor Request Form...
Route::prefix('labor')->group(function() {
    Route::get('show_request_form', 'LaborRequestController@show_labor_request_form');
    Route::post('show_request_form', 'LaborRequestController@labor_request_form')->name('labor_request_form');
});


// labor attendance system 
Route::prefix('labor-system')->group(function () {
    Route::get('/', [LaborController::class, 'showLaborSystem'])->name('labor_system');
    Route::get('/laborers', [LaborController::class, 'getLaborers'])->name('labor.getLaborers');
    Route::get('/projects', [LaborController::class, 'getProjects'])->name('labor.getProjects');
    Route::post('/assign', [LaborController::class, 'assignLabor'])->name('labor.assignLabor');
    Route::get('/assignments', [LaborController::class, 'getLabourAssignments'])->name('labor.assignments');
});

// Other Labor-related Routes
Route::get('/visit-schedules', [LaborController::class, 'getVisitSchedules'])->name('labor.getVisitSchedules');
Route::get('/monthly-report', [LaborController::class, 'monthlyReport'])->name('labor.monthlyReport');
Route::get('/attendance/delete-group', [LaborController::class, 'deleteGroup'])->name('attendance.deleteGroup');
Route::get('/attendance/edit-group', [LaborController::class, 'editGroup'])->name('attendance.editGroup');
Route::post('/attendance/update-group', [LaborController::class, 'updateGroup'])->name('attendance.updateGroup');


Route::group(['middleware' => ['auth']], function () {
    Route::get('tickets/status', [TicketController::class, 'statusIndex'])->name('tickets.status');
    Route::post('tickets/update-status/{id}', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
});
Route::group(['middleware' => ['auth']], function () {
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('tickets/{id}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});

Route::group(['middleware' => ['auth']], function () {
    Route::resource('letters', 'LetterController')->except([
        'show', 'edit', 'update', 'destroy'
    ]);
    Route::get('letters/{letter}', 'LetterController@show')->name('letters.show');
    Route::get('letters/{letter}/edit', 'LetterController@edit')->name('letters.edit');
    Route::put('letters/{letter}', 'LetterController@update')->name('letters.update');
    Route::delete('letters/{letter}', 'LetterController@destroy')->name('letters.destroy');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/staff-ratings', 'StaffRatingController@index')->name('staff-ratings.index');
    Route::post('/staff-ratings/{stafProfile}', 'StaffRatingController@store')->name('staff-ratings.store');
    Route::get('staff-ratings/report', 'StaffRatingController@monthlyReport')->name('staff-ratings.report');
    Route::get('staff-ratings/timeline', 'StaffRatingController@adminTimeline')->name('staff-ratings.timeline');
});







// ============================================
// PWA ROUTES
// ============================================

Route::prefix('pwa')->group(function () {
    Route::get('/home', [App\Http\Controllers\PWAController::class, 'home'])->name('pwa.home');

    // ============================================
    // PUBLIC ROUTES (NO AUTH)
    // ============================================
    Route::get('/login', function () {
        return view('pwa.login');
    })->name('pwa.login');

    Route::get('/offline', function () {
        return view('pwa.offline');
    })->name('pwa.offline');

    // ============================================
    // PROTECTED ROUTES (SANCTUM AUTH)
    // ============================================

    Route::get('/absents', function () {
        return view('pwa.attendance');
    })->name('pwa.attendance');

    Route::get('/confirmation', function () {
        return view('pwa.confirmation');
    })->name('pwa.confirmation');

    Route::get('/absent-history', function () {
        return view('pwa.absent-history');
    })->name('pwa.history');

    // PAGE: Mark Present Attendance + Overtime
    Route::get('/presents', function () {
        return view('pwa.present');
    })->name('pwa.present');

    // ============================================
    // PROTECTED ROUTES - SITE MANAGEMENT
    // ============================================
    Route::get('/sites', function () {
        return view('pwa.sites.index');
    })->name('pwa.sites.index');

    Route::get('/sites/create', function () {
        return view('pwa.sites.create');
    })->name('pwa.sites.create');

    Route::get('/sites/{id}/edit', function ($id) {
        return view('pwa.sites.edit', ['siteId' => $id]);
    })->name('pwa.sites.edit');

    // ============================================
    // PROTECTED ROUTES - PRESENT MANAGEMENT
    // ============================================
    Route::get('/present-history', function () {
        return view('pwa.present-history');
    })->name('pwa.presents');

    // ============================================
    // QR STAFF ATTENDANCE SCANNER (PWA)
    // ============================================
    Route::get('/qr-scanner', function () {
        return view('pwa.qr-scanner');
    })->name('pwa.qr-scanner');

    // ============================================
    // CATCH-ALL FOR SPA ROUTING
    // ============================================
    // Route::get('/{any}', function () {
    //     return view('pwa.attendance');
    // })->where('any', '.*');
});


Route::middleware('auth')->group(function () {
    // Pending Attendance Routes
    Route::get('/pending-attendance', [App\Http\Controllers\PendingAttendanceController::class, 'index'])->name('pending.attendance');
    Route::get('/pending-attendance/data', [App\Http\Controllers\PendingAttendanceController::class, 'data'])->name('pending.attendance.data');
    Route::post('/pending-attendance/{id}/approve', [App\Http\Controllers\PendingAttendanceController::class, 'approve'])->name('pending.attendance.approve');
    Route::post('/pending-attendance/{id}/reject', [App\Http\Controllers\PendingAttendanceController::class, 'reject'])->name('pending.attendance.reject');
    Route::get('/pending-attendance/export', [App\Http\Controllers\PendingAttendanceController::class, 'absentReportExport'])->name('pending.attendance.export');
    
    // Present Attendance Routes
    Route::get('/attendance/presents', [App\Http\Controllers\PendingAttendanceController::class, 'presentsIndex'])->name('attendance.presents');
    Route::get('/attendance/presents/data', [App\Http\Controllers\PendingAttendanceController::class, 'presentsData'])->name('attendance.presents.data');
    Route::get('/attendance/presents/export', [App\Http\Controllers\PendingAttendanceController::class, 'presentsReportExport'])->name('attendance.presents.export');

    // QR Staff Attendance Review Routes
    Route::get('/attendance/qr-overtime', [QrStaffAttendanceAdminController::class, 'index'])->name('attendance.qr-overtime.index');
    Route::get('/attendance/qr-overtime/data', [QrStaffAttendanceAdminController::class, 'data'])->name('attendance.qr-overtime.data');
    Route::post('/attendance/qr-overtime/{id}/review', [QrStaffAttendanceAdminController::class, 'review'])->name('attendance.qr-overtime.review');

    // ============================================
    // QR + GEOLOCATION ATTENDANCE ROUTES
    // ============================================
    Route::prefix('qr-attendance')->name('qr-attendance.')->group(function () {
        // Employee Scanner Interface - Logged-in user only
        Route::get('/', function () {
            return view('qr-attendance.index');
        })->name('index');

        // Confirmation page after successful scan
        Route::get('/confirmation', function () {
            return view('qr-attendance.confirmation');
        })->name('confirmation');

        // Reporting Routes
        Route::get('/report/user/{userId}', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'userReport'])->name('user-report');
        Route::get('/report/all', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'allUsersReport'])->name('all-users-report');
        Route::get('/export/user/{userId}', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'exportUserReport'])->name('export-user');
        Route::get('/export/all', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'exportAllUsersReport'])->name('export-all');

        // API Data Endpoints for AJAX filtering
        Route::get('/api/user-report-data/{userId}', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'getUserReportData'])->name('api.user-report-data');
        Route::get('/api/all-users-report-data', [App\Http\Controllers\API\Attendance\QRAttendanceController::class, 'getAllUsersReportData'])->name('api.all-users-report-data');
    });
});

// TEMP: trigger morning attendance summary manually
Route::get('/temp/send-morning-summary', function () {
    \Illuminate\Support\Facades\Artisan::call('attendance:send-morning-summary');
    return '<pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
});