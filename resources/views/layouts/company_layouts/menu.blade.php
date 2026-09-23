@canany(['companies','quotations','invoices','lpoins','projects','stafprofile'])
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('companies*') || Request::is('quotations*') || Request::is('invoices*') || Request::is('lpoins*') || Request::is('projects*') || Request::is('staf*') || Request::is('document*') ? 'active' : '' }}">Operation</a>
    <ul class="dropdown-menu border-0 shadow">
        @can('companies')
            <li>
                <a href="{{ route('companies.index') }}" class="dropdown-item {{ Request::is('companies*') ? 'active' : '' }}">@lang('models/companies.plural')</a>
            </li>
        @endcan
        @can('quotations')
            <li>
                <a href="{{ route('quotations.index') }}" class="dropdown-item {{ Request::is('quotations*') ? 'active' : '' }}">@lang('models/quotations.plural')</a>
            </li>
        @endcan
        @can('invoices')
            <li>
                <a href="{{ route('invoices.index') }}" class="dropdown-item {{ Request::is('invoices*') ? 'active' : '' }}">@lang('models/invoices.plural')</a>
            </li>
        @endcan
        @can('lpoins')
            <li>
                <a href="{{ route('lpoins.index') }}" class="dropdown-item {{ Request::is('lpoins*') ? 'active' : '' }}">@lang('models/lpoins.plural')</a>
            </li>
        @endcan
        @can('projects')
            <li>
                <a href="{{ route('projects.index') }}" class="dropdown-item {{ Request::is('projects*') ? 'active' : '' }}">@lang('models/projects.plural')</a>
            </li>
        @endcan
        @can('stafprofile')
            <li>
                <a href="{{ route('staf.index') }}" class="dropdown-item {{ Request::is('staf*') ? 'active' : '' }}">@lang('models/stafprofile.front')</a>
            </li>
        @endcan
        @can('document')
            <li>
                <a href="{{ route('document.index') }}" class="dropdown-item {{ Request::is('document*') ? 'active' : '' }}">@lang('models/document.front')</a>
            </li>
        @endcan
        @can('tasks')
            <li>
                <a href="{{ route('task.index') }}" class="dropdown-item {{ Request::is('task*') ? 'active' : '' }}">@lang('models/tasks.front')</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

@canany(['vendors','paymentInvoices','lpoouts'])
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('vendors*') || Request::is('paymentInvoices*') || Request::is('lpoouts*') ? 'active' : '' }}">Trade</a>
    <ul class="dropdown-menu border-0 shadow">
        @can('vendors')
            <li>
                <a href="{{ route('vendors.index') }}" class="dropdown-item {{ Request::is('vendors*') ? 'active' : '' }}">@lang('models/vendors.plural')</a>
            </li>
        @endcan
        @can('paymentInvoices')
            <li>
                <a href="{{ route('paymentInvoices.index') }}" class="dropdown-item {{ Request::is('paymentInvoices*') ? 'active' : '' }}">@lang('models/payment_invoices.plural')</a>
            </li>
        @endcan
        @can('lpoouts')
            <li>
                <a href="{{ route('lpoouts.index') }}" class="dropdown-item {{ Request::is('lpoouts*') ? 'active' : '' }}">@lang('models/lpoouts.plural')</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

@can('invoiceRequests')
    <li class="nav-item">
        <a href="{{ route('invoiceRequests.index') }}" class="nav-link {{ Request::is('invoiceRequests*') ? 'active' : '' }}">@lang('models/invoice_requests.plural')</a>
    </li>
@endcan


@canany(['receipts','payments'])
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('receipts*') || Request::is('payments*') ? 'active' : '' }}">Receipt / Payment</a>
    <ul class="dropdown-menu border-0 shadow">
        @can('receipts')
            <li>
                <a href="{{ route('receipts.index') }}" class="dropdown-item {{ Request::is('receipts*') ? 'active' : '' }}">@lang('models/receipts.plural')</a>
            </li>
        @endcan
       @can('payments')
            <li>
                <a href="{{ route('payments.index') }}" class="dropdown-item {{ Request::is('payments*') ? 'active' : '' }}">@lang('models/payments.plural')</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

@can('payroll')
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('staffPayrolls*') || Request::is('staffPayrolls*') ? 'active' : '' }}">Payroll</a>
    <ul class="dropdown-menu border-0 shadow">
        <li>
            <a href="{{ route('staffPayrolls.index') }}" class="dropdown-item {{ Request::is('staffPayrolls*') ? 'active' : '' }}">@lang('models/staff_payrolls.title')</a>
        </li>
    </ul>
</li>
@endcan

@canany(['accounts','cheques'])
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('accounts*') || Request::is('cheques*') ? 'active' : '' }}">Wallet</a>
    <ul class="dropdown-menu border-0 shadow">
        @can('accounts')
            <li>
                <a href="{{ route('accounts.index') }}" class="dropdown-item {{ Request::is('accounts*') ? 'active' : '' }}">@lang('models/accounts.plural')</a>
            </li>
        @endcan
        @can('cheques')
            <li>
                <a href="{{ route('cheques.index') }}" class="dropdown-item {{ Request::is('cheques*') ? 'active' : '' }}">@lang('models/cheques.plural')</a>
            </li>
        @endcan
        @can('pettyCashes')
            <li>
                <a href="{{ route('pettyCashes.index') }}" class="dropdown-item {{ Request::is('pettyCashes*') ? 'active' : '' }}">@lang('models/petty_cashes.plural')</a>
            </li>
        @endcan
        @can('pettyCashes')
        <li>
            <a href="{{ route('pettyCashExpenses.index') }}" class="dropdown-item {{ Request::is('pettyCashExpenses*') ? 'active' : '' }}">@lang('models/petty_cash_expenses.plural')</a>
        </li>
    @endcan
    </ul>
</li>
@endcan

@canany(['roles','users','employees'])
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('users*') || Request::is('roles*') ? 'active' : '' }}">Authentication</a>
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
                <a href="{{ route('employees.index') }}" class="dropdown-item {{ Request::is('employees*') ? 'active' : '' }}">@lang('models/employees.plural')</a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

@can('requestForms')
    <li class="nav-item">
        <a href="{{ route('requestForms.index') }}" class="nav-link {{ Request::is('requestForms*') ? 'active' : '' }}">@lang('models/request_forms.plural')</a>
    </li>
@endcan


@can('report_pettycash')
<li class="nav-item dropdown">
    <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle {{ Request::is('reports*') ? 'active' : '' }}">@lang('models/reports.plural')</a>
    <ul class="dropdown-menu border-0 shadow">
        <li>
            <a href="{{ route('reports.petty_cash_users') }}" class="dropdown-item {{ Request::is('petty_cash_users*') ? 'active' : '' }}">@lang('models/reports.pettycash.users.title')</a>
        </li>
        <li>
            <a href="{{ route('reports.petty_cash_register') }}" class="dropdown-item {{ Request::is('petty_cash_register*') ? 'active' : '' }}">@lang('models/reports.pettycash.register.title')</a>
        </li>
        <li>
            <a href="{{ route('reports.payment') }}" class="dropdown-item {{ Request::is('reports/payment*') ? 'active' : '' }}">@lang('models/reports.payment.title')</a>
        </li>
        <li>
            <a href="{{ route('reports.receipt') }}" class="dropdown-item {{ Request::is('reports/receipt*') ? 'active' : '' }}">@lang('models/reports.receipt.title')</a>
        </li>
        <li>
            <a href="{{ route('reports.invoice') }}" class="dropdown-item {{ Request::is('reports/invoice*') ? 'active' : '' }}">@lang('models/reports.invoice.title')</a>
        </li>
        <li>
            <a href="{{ route('reports.account') }}" class="dropdown-item {{ Request::is('reports/account*') ? 'active' : '' }}">@lang('models/reports.account.title')</a>
        </li>
    </ul>
</li>
@endcan
