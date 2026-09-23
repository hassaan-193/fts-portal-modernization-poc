<?php

namespace App\Http\Controllers;

use App\DataTables\AccountReportDataTable;
use App\DataTables\InvoiceReportDataTable;
use App\DataTables\PaymentReportDataTable;
use App\DataTables\PettyCashUserDataTable;
use App\DataTables\ReceiptReportDataTable;
use App\DataTables\TrialBalanceReportDataTable;
use App\Http\Controllers\AppBaseController;
use App\DataTables\PettyCashRegisterDataTable;

class ReportController extends AppBaseController
{
    public function petty_cash_users(PettyCashUserDataTable $pettycashUsersDatatable)
    {
        return $pettycashUsersDatatable->render('reports.pettycash.users');
    }

    public function petty_cash_register(PettyCashRegisterDataTable $pettycashregisterDatatable)
    {
        return $pettycashregisterDatatable->render('reports.pettycash.register');
    }
    public function payment(PaymentReportDataTable $PaymentReport)
    {
        return $PaymentReport->render('reports.payment');
    }
    public function receipt(ReceiptReportDataTable $receipReport)
    {
        return $receipReport->render('reports.receipt');
    }
    public function invoice(InvoiceReportDataTable $receipReport)
    {
        return $receipReport->render('reports.invoice');
    }
    public function account(AccountReportDataTable $accountReport)
    {
        return $accountReport->render('reports.account');
    }
    
    public function trial_balance(TrialBalanceReportDataTable $trialBalanceReport)
    {
        return $trialBalanceReport->render('reports.trial_balance');
    }
}
