<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu > .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: 0;
        display: none;
        position: absolute;
        z-index: 1000;
    }

    .dropdown-submenu:hover > .dropdown-menu {
        display: block;
    }
    #projectsSubMenu:hover{
        background-color: red !important;

    }
</style>

{{-- <li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('/*') ? 'active' : '' }}">Dashboard</a>
</li> --}}
{{-- AMC Reporter: standalone menu item for users who only have amc_report permission --}}
@if(auth()->check() && auth()->user()->can('amc_report') && !auth()->user()->can('projects'))
<li class="nav-item {{ Request::routeIs('projects.showForm') ? 'active' : '' }}">
    <a href="{{ route('projects.showForm') }}" class="nav-link">Submit AMC Report</a>
</li>
<li class="nav-item {{ Request::routeIs('projects.drafts') ? 'active' : '' }}">
    <a href="{{ route('projects.drafts') }}" class="nav-link">My AMC Drafts</a>
</li>
@endif

{{-- AMC Report Approver: standalone menu item for users who only have approve_amc_reports permission --}}
@if(auth()->check() && auth()->user()->can('approve_amc_reports') && !auth()->user()->can('projects'))
<li class="nav-item {{ Request::routeIs('projects.reportStatus') ? 'active' : '' }}">
    <a href="{{ route('projects.reportStatus') }}" class="nav-link">AMC Report Status</a>
</li>
@endif

{{-- Staff Requester: standalone menu item for users who only have own_request_form permission --}}
@if(auth()->check() && auth()->user()->can('own_request_form') && !auth()->user()->can('stafprofile'))
<li class="nav-item {{ Request::routeIs('own_requests.*') ? 'active' : '' }}">
    <a href="{{ route('own_requests.index') }}" class="nav-link">My Requests</a>
</li>
@endif

@canany(['companies', 'quotations', 'invoices', 'lpoins', 'projects', 'stafprofile', 'approve_staff_requests', 'approve_labor_requests'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('companies*') || Request::is('quotations*') || Request::is('invoices*') || Request::is('lpoins*') || Request::is('projects*') || Request::is('staf*') || Request::is('document*') || Request::is('request-approvals*') ? 'active' : '' }}">Operation</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('companies')
                <li>
                    <a href="{{ route('companies.index') }}"
                        class="dropdown-item {{ Request::is('companies*') ? 'active' : '' }}">@lang('models/companies.plural')</a>
                </li>
            @endcan
            @can('quotations')
                <li>
                    <a href="{{ route('quotations.index') }}"
                        class="dropdown-item {{ Request::is('quotations*') ? 'active' : '' }}">@lang('models/quotations.plural')</a>
                </li>
            @endcan
            @can('invoices')
                <li>
                    <a href="{{ route('invoices.index') }}"
                        class="dropdown-item {{ Request::is('invoices*') ? 'active' : '' }}">@lang('models/invoices.plural')</a>
                </li>
            @endcan
            @can('lpoins')
                <li>
                    <a href="{{ route('lpoins.index') }}"
                        class="dropdown-item {{ Request::is('lpoins*') ? 'active' : '' }}">@lang('models/lpoins.plural')</a>
                </li>
            @endcan
            @can('projects')
                <li>
                    <a href="{{ route('projects.index') }}"
                    class="dropdown-item {{ Request::routeIs('projects.index') ? 'active' : '' }}">
                    @lang('models/projects.plural')
                    </a>
                </li>
            @endcan

            @can('projects')
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" id="projectsSubMenu"  style="background-color: #343a40;">
                    AMC
                </a>
                <ul class="dropdown-menu" aria-labelledby="projectsSubMenu">
                    <li>
                        <a href="{{ route('projects.visit-tracking') }}"
                        class="dropdown-item {{ Request::routeIs('projects.visit-tracking') ? 'active' : '' }}">
                        @lang('models/projects.Track')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('visit-schedules.history') }}"
                        class="dropdown-item {{ Request::routeIs('visit-schedules.history') ? 'active' : '' }}">
                        @lang('models/projects.History')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.reportStatus') }}"
                        class="dropdown-item {{ Request::routeIs('projects.reportStatus') ? 'active' : '' }}">
                        @lang('models/projects.ReportStatus')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.showForm') }}"
                        class="dropdown-item {{ Request::routeIs('projects.showForm') ? 'active' : '' }}">
                        @lang('models/projects.VisitForm')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.drafts') }}"
                        class="dropdown-item {{ Request::routeIs('projects.drafts') ? 'active' : '' }}">
                        @lang('models/projects.Drafts')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('amc.invoices.index') }}"
                        class="dropdown-item {{ Request::routeIs('amc.invoices.index') ? 'active' : '' }}">
                        @lang('models/projects.AmcInvoices')
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('stafprofile')
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" id="staffRatingSubMenu" style="background-color: #343a40;">
                    Staff Ratings
                </a>
                <ul class="dropdown-menu" aria-labelledby="staffRatingSubMenu">
                    <li>
                        <a href="{{ route('staff-ratings.index') }}"
                            class="dropdown-item {{ Request::is('staff-ratings') ? 'active' : '' }}">
                            Rate Staff
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff-ratings.report') }}"
                            class="dropdown-item {{ Request::is('staff-ratings/report') ? 'active' : '' }}">
                            Monthly Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff-ratings.timeline') }}"
                            class="dropdown-item {{ Request::is('staff-ratings/timeline') ? 'active' : '' }}">
                            Timeline
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('stafprofile')
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" id="projectsSubMenu"  style="background-color: #343a40;">
                    Staff
                </a>
                <ul class="dropdown-menu" aria-labelledby="projectsSubMenu">
                    <li>
                        <a href="{{ route('staf.index') }}"
                            class="dropdown-item {{ Request::is('staf') ? 'active' : '' }}">@lang('models/stafprofile.front')</a>
                    </li>
                    <li>
                        <a href="{{ route('staf-profile.expiry-list') }}"
                            class="dropdown-item {{ Request::is('staf-profile/expiry-list') ? 'active' : '' }}">@lang('models/stafprofile.expiry')</a>
                    </li>
                    <li>
                        <a href="{{ route('staf-dates.index') }}"
                            class="dropdown-item {{ Request::is('staf-dates*') ? 'active' : '' }}">Staff Leave Periods</a>
                    </li>
                </ul>
            </li>
            @endcan
            @canany(['approve_staff_requests', 'approve_labor_requests'])
                <li>
                    <a href="{{ route('request_approvals.index') }}"
                        class="dropdown-item {{ Request::is('request-approvals*') ? 'active' : '' }}">Request Approvals</a>
                </li>
            @endcanany
            @can('document')
                <li>
                    <a href="{{ route('document.index') }}"
                        class="dropdown-item {{ Request::is('document*') ? 'active' : '' }}">@lang('models/document.front')</a>
                </li>
            @endcan
            @can('tasks')
                <li>
                    <a href="{{ route('task.index') }}"
                        class="dropdown-item {{ Request::is('task*') ? 'active' : '' }}">@lang('models/tasks.front')</a>
                </li>
            @endcan
            @can('products')
                <li class="nav-item">
                    <a href="{{ route('products.index') }}"
                        class="dropdown-item {{ Request::is('products*') ? 'active' : '' }}">@lang('models/products.plural')</a>
                </li>
            @endcan
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" id="projectsSubMenu"  style="background-color: #343a40;">
                    Tickets
                </a>
                <ul class="dropdown-menu" aria-labelledby="projectsSubMenu">
                    @can('ticket_creation')
                        <li>
                            <a href="{{ route('tickets.index') }}"
                                class="dropdown-item {{ Request::is('tickets') ? 'active' : '' }}">Tickets</a>
                        </li>
                    @endcan
        
                    @can('ticket_status')
                        <li>
                            <a href="{{ route('tickets.status') }}"
                                class="dropdown-item {{ Request::is('tickets/status*') ? 'active' : '' }}">Ticket Status</a>
                        </li>
                    @endcan
                </ul>
            </li>
            @can('letters')
                <li class="nav-item">
                    <a href="{{ route('letters.index') }}"
                    class="dropdown-item {{ Request::is('letters*') ? 'active' : '' }}">Letters</a>
                </li>
            @endcan
            @can('vendor_order')

                <li class="nav-item">
                    <a href="{{ route('orders.index') }}"
                        class="dropdown-item {{ Request::is('products*') ? 'active' : '' }}">Vendor Orders</a>
                </li>
            @endcan

            {{-- Inquiry Management System --}}
            @if(auth()->user()->hasRole('inquiry'))
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" style="background-color: #343a40;">
                     Inquiries
                </a>
                <ul class="dropdown-menu" aria-labelledby="inquiriesSubMenu">
                    @if(auth()->user()->hasRole('Administration'))
                        <li>
                            <a href="{{ route('inquiries.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.index') ? 'active' : '' }}">
                                 All Inquiries
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('inquiries.engineer.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.engineer.index') ? 'active' : '' }}">
                                 Site Visits
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('inquiries.department.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.department.index') ? 'active' : '' }}">
                                 Department Inquiries
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('inquiries.sales.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.sales.index') ? 'active' : '' }}">
                                 Sales 
                            </a>
                        </li>
                    @elseif(auth()->user()->hasRole('Engineer'))
                        <li>
                            <a href="{{ route('inquiries.engineer.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.engineer.index') ? 'active' : '' }}">
                                 Site Visits
                            </a>
                        </li>
                    @elseif(auth()->user()->hasRole('Sales'))
                        <li>
                            <a href="{{ route('inquiries.sales.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.sales.index') ? 'active' : '' }}">
                                 Sales 
                            </a>
                        </li>
                    @else
                        {{-- Department users --}}
                        <li>
                            <a href="{{ route('inquiries.department.index') }}"
                                class="dropdown-item {{ Request::routeIs('inquiries.department.index') ? 'active' : '' }}">
                                 Department Inquiries
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif
            @can('labor_attendance')
                @unless(auth()->user()->hasRole('Lpoouts-QR-Viewer'))
                <li class="nav-item dropdown-submenu position-relative">
                    <a class="dropdown-item" href="#" id="projectsSubMenu"  style="background-color: #343a40;">
                        Labor system
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="projectsSubMenu">
                        <li>
                            <a href="{{ route('labor_system') }}"
                                class="dropdown-item {{ Request::is('labor-system') ? 'active' : '' }}">Labor Attendance</a>
                        </li>
                        <li>
                            <a href="{{ route('labor.assignments') }}"
                                class="dropdown-item {{ Request::is('labor-system/assignments') ? 'active' : '' }}">View Labor Attendance</a>
                        </li>
                        <li>
                            <a href="{{ route('pending.attendance') }}"
                                class="dropdown-item {{ Request::is('pending-attendance') ? 'active' : '' }}">View Absentees</a>
                        </li>
                        <li>
                            <a href="{{ route('attendance.presents') }}"
                                class="dropdown-item {{ Request::is('attendance/presents') ? 'active' : '' }}">View Overtime</a>
                        </li>


                        <li>
                            <a href="{{ route('attendance.qr-overtime.index') }}"
                                class="dropdown-item {{ Request::is('attendance/qr-overtime') ? 'active' : '' }}">Labor Qr Attendance Review</a>
                        </li>
                    </ul>
                </li>
                @endunless
            @endcan

    <li class="nav-item dropdown-submenu position-relative">
        <a class="dropdown-item" href="#" style="background-color: #343a40;">
            Drawing Receiveds
        </a>
        <ul class="dropdown-menu" aria-labelledby="projectsSubMenu">
            <li>
                <a href="{{ route('drawing-receiveds.index') }}"
                    class="dropdown-item {{ Request::is('drawing-receiveds') ? 'active' : '' }}">
                    All Drawings
                </a>
            </li>
            <li>
                <a href="{{ route('drawing-receiveds.engineer-dashboard') }}"
                    class="dropdown-item {{ Request::routeIs('drawing-receiveds.engineer-dashboard') ? 'active' : '' }}">
                    Engineer Dashboard
                </a>
            </li>
        </ul>
    </li>
        </ul>
    </li>


@endcanany

@can('labor_attendance')
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('qr-attendance*') ? 'active' : '' }}">QR Attendance</a>
        <ul class="dropdown-menu border-0 shadow">
            <li>
                <a href="{{ route('qr-attendance.index') }}"
                    class="dropdown-item {{ Request::is('qr-attendance') ? 'active' : '' }}">Scan QR</a>
            </li>
            <li>
                <a href="{{ route('qr-attendance.all-users-report') }}"
                    class="dropdown-item {{ Request::is('qr-attendance/report/all') ? 'active' : '' }}">Attendance Report</a>
            </li>
        </ul>
    </li>
@endcan

@canany(['vendors', 'paymentInvoices', 'lpoouts'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('vendors*') || Request::is('paymentInvoices*') || Request::is('lpoouts*') || Request::is('purchase-orders*') || Request::is('vendor-payables*') || Request::is('payment-analytics*') ? 'active' : '' }}">Trade</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('vendors')
                <li>
                    <a href="{{ route('vendors.index') }}"
                        class="dropdown-item {{ Request::is('vendors*') ? 'active' : '' }}">@lang('models/vendors.plural')</a>
                </li>
            @endcan
            @can('paymentInvoices')
                <li>
                    <a href="{{ route('paymentInvoices.index') }}"
                        class="dropdown-item {{ Request::is('paymentInvoices*') ? 'active' : '' }}">@lang('models/payment_invoices.plural')</a>
                </li>
            @endcan
            @can('lpoouts')
                <li>
                    <a href="{{ route('lpoouts.index') }}"
                        class="dropdown-item {{ Request::is('lpoouts') ? 'active' : '' }}">@lang('models/lpoouts.plural')</a>
                </li>
            @endcan
            @unless(auth()->user()->hasRole('Lpoouts-QR-Viewer'))
            <li class="nav-item dropdown-submenu position-relative">
                <a class="dropdown-item" href="#" id="purchaseOrdersSubMenu"  style="background-color: #343a40;">
                    Purchase Orders
                </a>
                <ul class="dropdown-menu" aria-labelledby="purchaseOrdersSubMenu">
                    <li>
                        <a href="{{ route('purchase-orders.index') }}"
                            class="dropdown-item {{ Request::is('purchase-orders') ? 'active' : '' }}">PO Request</a>
                    </li>
                    <li>
                        <a href="{{ route('purchase-orders.departmentIndex') }}"
                            class="dropdown-item {{ Request::is('purchase-orders-department/index') ? 'active' : '' }}">Department Review</a>
                    </li>
                    <li>
                        <a href="{{ route('purchase-orders.adminIndex') }}"
                            class="dropdown-item {{ Request::is('purchase-orders-admin/index') ? 'active' : '' }}">Admin Approval</a>
                    </li>
                </ul>
            </li>
            {{-- Vendor Payables --}}
            <li>
                <a href="{{ route('vendor-payables.index') }}"
                    class="dropdown-item {{ Request::is('vendor-payables*') ? 'active' : '' }}">
                    Vendor Payables
                </a>
            </li>
            {{-- Payment Analytics --}}
            <li>
                <a href="{{ route('payment-analytics.index') }}"
                    class="dropdown-item {{ Request::is('payment-analytics*') ? 'active' : '' }}">
                    Payment Analytics
                </a>
            </li>
            @endunless
        </ul>
    </li>
@endcanany

@canany(['invoiceRequests', 'laborRequests'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('invoiceRequests*') || Request::is('laborRequests*') || Request::is('staffRequests*') ? 'active' : '' }}">Requests</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('invoiceRequests')
                <li>
                    <a href="{{ route('invoiceRequests.index') }}"
                        class="dropdown-item {{ Request::is('invoiceRequests*') ? 'active' : '' }}">@lang('models/invoice_requests.plural')</a>
                </li>
            @endcan
            @can('laborRequests')
                <li>
                    <a href="{{ route('laborRequests.index') }}"
                        class="dropdown-item {{ Request::is('laborRequests*') ? 'active' : '' }}">@lang('models/labor_requests.title')</a>
                </li>
            @endcan
            @can('staffRequests')
                <li>
                    <a href="{{ route('staff_requests') }}"
                        class="dropdown-item {{ Request::is('staffRequests*') ? 'active' : '' }}">@lang('models/stafprofile.request')</a>
                </li>
            @endcan
            @can('requestForms')
                <li >
                    <a href="{{ route('requestForms.index') }}"
                        class="dropdown-item {{ Request::is('requestForms*') ? 'active' : '' }}">@lang('models/request_forms.plural')</a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

@canany(['receipts', 'payments'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('receipts*') || Request::is('payments*') ? 'active' : '' }}">Receipt
            / Payment</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('receipts')
                <li>
                    <a href="{{ route('receipts.index') }}"
                        class="dropdown-item {{ Request::is('receipts*') ? 'active' : '' }}">@lang('models/receipts.plural')</a>
                </li>
            @endcan
            @can('payments')
                <li>
                    <a href="{{ route('payments.index') }}"
                        class="dropdown-item {{ Request::is('payments*') ? 'active' : '' }}">@lang('models/payments.plural')</a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

@can('payroll')
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('staffPayrolls*') || Request::is('staffPayrolls*') ? 'active' : '' }}">Payroll</a>
        <ul class="dropdown-menu border-0 shadow">
            <li>
                <a href="{{ route('staffPayrolls.index') }}"
                    class="dropdown-item {{ Request::is('staffPayrolls*') ? 'active' : '' }}">@lang('models/staff_payrolls.title')</a>
            </li>
        </ul>
    </li>
@endcan

@canany(['accounts', 'cheques'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('accounts*') || Request::is('cheques*') ? 'active' : '' }}">Wallet</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('accounts')
                <li>
                    <a href="{{ route('accounts.index') }}"
                        class="dropdown-item {{ Request::is('accounts*') ? 'active' : '' }}">@lang('models/accounts.plural')</a>
                </li>
            @endcan
            @can('cheques')
                <li>
                    <a href="{{ route('cheques.index') }}"
                        class="dropdown-item {{ Request::is('cheques*') ? 'active' : '' }}">@lang('models/cheques.plural')</a>
                </li>
            @endcan
            @can('pettyCashes')
                <li>
                    <a href="{{ route('pettyCashes.index') }}"
                        class="dropdown-item {{ Request::is('pettyCashes*') ? 'active' : '' }}">@lang('models/petty_cashes.plural')</a>
                </li>
            @endcan
            @can('pettyCashes')
                <li>
                    <a href="{{ route('pettyCashExpenses.index') }}"
                        class="dropdown-item {{ Request::is('pettyCashExpenses*') ? 'active' : '' }}">@lang('models/petty_cash_expenses.plural')</a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
<!-- @canany(['accounts', 'cheques'])

<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle {{ Request::is('manualJournals*') || Request::is('bulkUpdate*') || Request::is('vatPayments*') || Request::is('taxAdjustments*') || Request::is('currencyAdjustments*') || Request::is('chartOfAccounts*') || Request::is('budgets*') || Request::is('transactionLocking*') ? 'active' : '' }}">
        Accountant
    </a>
    <ul class="dropdown-menu border-0 shadow">

        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('manualJournals*') ? 'active' : '' }}">
                Manual Journals
            </a>
        </li>
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('bulkUpdate*') ? 'active' : '' }}">
                Bulk Update
            </a>
        </li>
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('vatPayments*') ? 'active' : '' }}">
                VAT Payments
            </a>
        </li>
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('taxAdjustments*') ? 'active' : '' }}">
                Tax Adjustments
            </a>
        </li>
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('currencyAdjustments*') ? 'active' : '' }}">
                Currency Adjustments
            </a>
        </li>
        @can('accounts')
                <li>
                    <a href="{{ route('accounts.index') }}"
                        class="dropdown-item {{ Request::is('accounts*') ? 'active' : '' }}">Chart of Accounts</a>
                </li>
        @endcan
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('budgets*') ? 'active' : '' }}">
                Budgets
            </a>
        </li>
        <li>
            <a href="#"
                class="dropdown-item {{ Request::is('transactionLocking*') ? 'active' : '' }}">
                Transaction Locking
            </a>
        </li>
    </ul>
</li>

@endcan -->



@canany(['roles', 'users', 'employees'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('users*') || Request::is('roles*') ? 'active' : '' }}">Authentication</a>
        <ul class="dropdown-menu border-0 shadow">
            @can('roles')
                <li>
                    <a href="{{ route('roles.index') }}" class="dropdown-item {{ Request::is('roles*') ? 'active' : '' }}">
                        Roles
                    </a>
                </li>
            @endcan
            @can('users')
                <li>
                    <a href="{{ route('users.index') }}" class="dropdown-item {{ Request::is('users*') ? 'active' : '' }}">
                        Users
                    </a>
                </li>
            @endcan
            @can('employees')
                <li>
                    <a href="{{ route('employees.index') }}"
                        class="dropdown-item {{ Request::is('employees*') ? 'active' : '' }}">@lang('models/employees.plural')</a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany



@if(auth()->user()->hasRole('reports'))
    @can('report_pettycash')
        <li class="nav-item dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle {{ Request::is('reports*') ? 'active' : '' }}">@lang('models/reports.plural')</a>
            <ul class="dropdown-menu border-0 shadow">
                <li>
                    <a href="{{ route('reports.petty_cash_users') }}"
                        class="dropdown-item {{ Request::is('petty_cash_users*') ? 'active' : '' }}">@lang('models/reports.pettycash.users.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.petty_cash_register') }}"
                        class="dropdown-item {{ Request::is('petty_cash_register*') ? 'active' : '' }}">@lang('models/reports.pettycash.register.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.payment') }}"
                        class="dropdown-item {{ Request::is('reports/payment*') ? 'active' : '' }}">@lang('models/reports.payment.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.receipt') }}"
                        class="dropdown-item {{ Request::is('reports/receipt*') ? 'active' : '' }}">@lang('models/reports.receipt.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.invoice') }}"
                        class="dropdown-item {{ Request::is('reports/invoice*') ? 'active' : '' }}">@lang('models/reports.invoice.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.account') }}"
                        class="dropdown-item {{ Request::is('reports/account*') ? 'active' : '' }}">@lang('models/reports.account.title')</a>
                </li>
                <li>
                    <a href="{{ route('reports.trial_balance') }}"
                        class="dropdown-item {{ Request::is('reports/trial-balance*') ? 'active' : '' }}">@lang('models/reports.trial_balance.title')</a>
                </li>
            </ul>
        </li>
    @endcan
@endif

{{--
    Payment Bookings — its own top-level menu, sitting beside Reports.

    Two audiences share the dropdown: accountants who book payments (`paymentBookings`)
    and the two review levels (`verify_` / `approve_payment_bookings`). Each item is
    gated for the audience that can actually use it, so a reviewer never sees a link
    that would 403, and the parent only renders when at least one item would.
--}}
@canany(['paymentBookings', 'verify_payment_bookings', 'approve_payment_bookings'])
    <li class="nav-item dropdown">
        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle {{ Request::is('payment-bookings*') ? 'active' : '' }}">@lang('models/payment_bookings.title')</a>
        <ul class="dropdown-menu border-0 shadow">
            <li>
                <a href="{{ route('payment-bookings.index') }}"
                    class="dropdown-item {{ Request::is('payment-bookings') ? 'active' : '' }}">@lang('models/payment_bookings.menu.index')</a>
            </li>
            @can('paymentBookings')
                <li>
                    <a href="{{ route('payment-bookings.create') }}"
                        class="dropdown-item {{ Request::is('payment-bookings/create') ? 'active' : '' }}">@lang('models/payment_bookings.menu.book')</a>
                </li>
            @endcan
            @canany(['verify_payment_bookings', 'approve_payment_bookings'])
                <li>
                    <a href="{{ route('payment-bookings.approvals') }}"
                        class="dropdown-item {{ Request::is('payment-bookings/approvals') ? 'active' : '' }}">@lang('models/payment_bookings.menu.approvals')</a>
                </li>
            @endcanany
            <li>
                <a href="{{ route('payment-bookings.dashboard') }}"
                    class="dropdown-item {{ Request::is('payment-bookings/dashboard') ? 'active' : '' }}">@lang('models/payment_bookings.menu.dashboard')</a>
            </li>
        </ul>
    </li>
@endcanany

