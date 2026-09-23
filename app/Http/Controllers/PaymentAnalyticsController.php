<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentInvoice;
use App\Models\Lpoout;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\Lookup;
use Carbon\Carbon;

class PaymentAnalyticsController extends AppBaseController
{
    public function index(Request $request)
    {
        $now       = Carbon::now();
        $today     = $now->toDateString();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd   = $now->copy()->endOfMonth()->toDateString();
        $next7      = $now->copy()->addDays(7)->toDateString();
        $next30     = $now->copy()->addDays(30)->toDateString();

        // =====================================================================
        // PIPELINE KPIs — driven from payment_invoices (both LPO + payable)
        // =====================================================================

        // Total pending (all sources, status=0)
        $totalPending = PaymentInvoice::where('status', 0)->sum('total_amount');
        $pendingCount = PaymentInvoice::where('status', 0)->count();

        // Total paid this month (via transactions)
        $paidThisMonth = DB::table('transactions')
            ->where('type', 'Payment')->where('status', 1)
            ->whereNotNull('transactionable_id')
            ->whereBetween('date_time', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
            ->sum('total');

        // Total paid this year
        $paidThisYear = DB::table('transactions')
            ->where('type', 'Payment')->where('status', 1)
            ->whereNotNull('transactionable_id')
            ->whereYear('date_time', $now->year)
            ->sum('total');

        // Overdue: status=0 and source_date < today (vendor payables with due date)
        $overdueAmount = PaymentInvoice::where('status', 0)
            ->whereNotNull('source_date')
            ->whereDate('source_date', '<', $today)
            ->sum('total_amount');
        $overdueCount = PaymentInvoice::where('status', 0)
            ->whereNotNull('source_date')
            ->whereDate('source_date', '<', $today)
            ->count();

        // Due this month
        $dueThisMonth = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $monthStart)
            ->whereDate('source_date', '<=', $monthEnd)
            ->sum('total_amount');

        // Due next 7 days
        $dueNext7 = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $today)
            ->whereDate('source_date', '<=', $next7)
            ->sum('total_amount');
        $dueNext7Count = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $today)
            ->whereDate('source_date', '<=', $next7)
            ->count();

        // Due next 30 days
        $dueNext30 = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $today)
            ->whereDate('source_date', '<=', $next30)
            ->sum('total_amount');
        $dueNext30Count = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $today)
            ->whereDate('source_date', '<=', $next30)
            ->count();

        // =====================================================================
        // MONTHLY EXECUTION: planned vs paid vs remaining
        // =====================================================================
        $plannedThisMonth = PaymentInvoice::where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('source_date', [$monthStart, $monthEnd])
                  ->orWhereNull('source_date');  // unscheduled pending also counts
            })->where('status', '!=', -1)->sum('total_amount');

        $paidInvoicesThisMonth = PaymentInvoice::where('status', 1)
            ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
            ->sum('total_amount');

        $remainingThisMonth = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '<=', $monthEnd)
            ->sum('total_amount');

        $completionThisMonth = ($plannedThisMonth > 0)
            ? round(($paidInvoicesThisMonth / $plannedThisMonth) * 100, 1)
            : 0;

        // =====================================================================
        // AGING ANALYSIS — pending payables only (vendor payables with due date)
        // =====================================================================
        $agingBase = PaymentInvoice::where('status', 0)->whereNotNull('source_date');

        $aging = [
            '0_30'  => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 0 AND 30",  [$today])->sum('total_amount'),
            '31_60' => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 31 AND 60", [$today])->sum('total_amount'),
            '61_90' => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 61 AND 90", [$today])->sum('total_amount'),
            '90+'   => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) > 90",              [$today])->sum('total_amount'),
        ];

        $agingCounts = [
            '0_30'  => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 0 AND 30",  [$today])->count(),
            '31_60' => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 31 AND 60", [$today])->count(),
            '61_90' => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) BETWEEN 61 AND 90", [$today])->count(),
            '90+'   => (clone $agingBase)->whereRaw("DATEDIFF(?, source_date) > 90",              [$today])->count(),
        ];

        // =====================================================================
        // VENDOR LIABILITY RANKING — pending amounts per vendor (top 15)
        // =====================================================================
        $vendorLiability = DB::table('payment_invoices')
            ->join('vendors', function ($j) {
                $j->on('payment_invoices.vendor_id', '=', 'vendors.id');
            })
            ->where('payment_invoices.status', 0)
            ->selectRaw('
                vendors.id,
                vendors.name,
                COUNT(payment_invoices.id) as invoice_count,
                SUM(payment_invoices.total_amount) as pending_amount,
                MIN(payment_invoices.source_date) as oldest_due,
                MAX(payment_invoices.source_date) as latest_due
            ')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('pending_amount')
            ->limit(15)
            ->get();

        // Add unlinked payables (vendor_id = null) as one aggregated row
        $unlinkedPending = PaymentInvoice::where('status', 0)->whereNull('vendor_id')->sum('total_amount');
        $unlinkedCount   = PaymentInvoice::where('status', 0)->whereNull('vendor_id')->count();

        // =====================================================================
        // OVERDUE RANKING — most overdue obligations
        // =====================================================================
        $overdueList = PaymentInvoice::where('status', 0)
            ->whereNotNull('source_date')
            ->whereDate('source_date', '<', $today)
            ->with('vendor')
            ->orderBy('source_date', 'asc')
            ->limit(15)
            ->get()
            ->map(function ($p) use ($today) {
                $p->days_overdue = Carbon::parse($p->source_date)->diffInDays($today);
                return $p;
            });

        // =====================================================================
        // UPCOMING PAYMENTS TABLE (next 30 days)
        // =====================================================================
        $upcomingPayments = PaymentInvoice::where('status', 0)
            ->whereDate('source_date', '>=', $today)
            ->whereDate('source_date', '<=', $next30)
            ->with('vendor')
            ->orderBy('source_date', 'asc')
            ->limit(20)
            ->get();

        // =====================================================================
        // RECENTLY PAID
        // =====================================================================
        $recentlyPaid = PaymentInvoice::where('status', 1)
            ->with(['vendor', 'transaction.transaction_payment_type'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        // =====================================================================
        // MONTHLY TREND — last 12 months (paid amounts from transactions)
        // =====================================================================
        $trendLabels  = collect();
        $trendPaid    = collect();
        $trendPending = collect();

        $monthlyData = DB::table('transactions')
            ->selectRaw('DATE_FORMAT(date_time, "%Y-%m") as month, SUM(total) as total')
            ->where('type', 'Payment')->where('status', 1)
            ->whereNotNull('transactionable_id')
            ->where('date_time', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->format('Y-m');
            $trendLabels->push($now->copy()->subMonths($i)->format('M Y'));
            $trendPaid->push((float) ($monthlyData[$m] ?? 0));
        }

        // Pending by month (source_date grouping)
        $pendingByMonth = DB::table('payment_invoices')
            ->selectRaw('DATE_FORMAT(source_date, "%Y-%m") as month, SUM(total_amount) as total')
            ->where('status', 0)->whereNotNull('source_date')
            ->where('source_date', '>=', $now->copy()->subMonths(11)->startOfMonth()->toDateString())
            ->groupBy('month')
            ->pluck('total', 'month');

        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->format('Y-m');
            $trendPending->push((float) ($pendingByMonth[$m] ?? 0));
        }

        // =====================================================================
        // VENDOR PAYMENT STATUS SUMMARY
        // =====================================================================
        $vendorStatusSummary = DB::table('payment_invoices')
            ->join('vendors', 'payment_invoices.vendor_id', '=', 'vendors.id')
            ->selectRaw('
                vendors.id,
                vendors.name,
                SUM(CASE WHEN payment_invoices.status = 0 THEN payment_invoices.total_amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN payment_invoices.status = 1 THEN payment_invoices.total_amount ELSE 0 END) as paid_amount,
                COUNT(CASE WHEN payment_invoices.status = 0 THEN 1 END) as pending_count,
                COUNT(CASE WHEN payment_invoices.status = 1 THEN 1 END) as paid_count,
                MIN(CASE WHEN payment_invoices.status = 0 THEN payment_invoices.source_date END) as next_due
            ')
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('pending_amount')
            ->limit(20)
            ->get();

        // =====================================================================
        // APPROVED LPOs AWAITING PAYMENT (operational)
        // =====================================================================
        $lposAwaitingPayment = Lpoout::where('status', 'Approved')
            ->where('is_latest_revision', true)
            ->doesntHave('paymentInvoices')
            ->with('vendor')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // Payment method breakdown (for chart)
        $paymentMethods = DB::table('transactions')
            ->join('lookups', 'transactions.payment_type', '=', 'lookups.id')
            ->selectRaw('lookups.name as method, SUM(transactions.total) as total, COUNT(*) as count')
            ->where('transactions.type', 'Payment')->where('transactions.status', 1)
            ->whereYear('transactions.date_time', $now->year)
            ->groupBy('lookups.name')
            ->orderByDesc('total')
            ->get();

        // Sidebar dropdowns
        $vendors  = Vendor::orderBy('name')->pluck('name', 'id');
        $projects = Project::orderBy('id')->pluck('subject', 'id');

        return view('payment_analytics.index', compact(
            // Pipeline KPIs
            'totalPending', 'pendingCount',
            'paidThisMonth', 'paidThisYear',
            'overdueAmount', 'overdueCount',
            'dueThisMonth',
            'dueNext7', 'dueNext7Count',
            'dueNext30', 'dueNext30Count',
            // Monthly execution
            'plannedThisMonth', 'paidInvoicesThisMonth', 'remainingThisMonth', 'completionThisMonth',
            // Aging
            'aging', 'agingCounts',
            // Vendor liability
            'vendorLiability', 'unlinkedPending', 'unlinkedCount',
            // Lists
            'overdueList', 'upcomingPayments', 'recentlyPaid',
            // Trend chart data
            'trendLabels', 'trendPaid', 'trendPending',
            // Vendor status
            'vendorStatusSummary',
            // Operational
            'lposAwaitingPayment', 'paymentMethods',
            // Dropdowns
            'vendors', 'projects'
        ));
    }
}
