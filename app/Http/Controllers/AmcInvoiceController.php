<?php

namespace App\Http\Controllers;

use App\DataTables\AmcInvoiceDataTable;
use App\Models\Invoice;

class AmcInvoiceController extends Controller
{
public function index(\App\DataTables\AmcInvoiceDataTable $dataTable)
{
    // Summary Stats
    $stats = Invoice::whereHas('quotation', function ($q) {
        $q->whereRaw('LOWER(category) = "amc"');
    })
    ->selectRaw("
        COUNT(*) as total_invoices,
        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_invoices,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as complete_invoices,
        SUM(total_amount) as total_amount
    ")
    ->first();

    return $dataTable->render('invoices.amc_index', compact('stats'));
}
}
