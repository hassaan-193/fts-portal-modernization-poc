<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\PaymentBooking;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\Company;

echo "======================================================================\n";
echo "PROBE 2: DATABASE QUERY PROFILING (N+1 QUERY EXPLOSION AUDIT)\n";
echo "======================================================================\n\n";

function profileQueries(string $label, callable $callback) {
    $queries = [];
    $totalTime = 0.0;

    DB::flushQueryLog();
    DB::enableQueryLog();

    $listener = function ($query) use (&$queries, &$totalTime) {
        $queries[] = [
            'sql' => $query->sql,
            'time' => $query->time,
        ];
        $totalTime += $query->time;
    };

    DB::listen($listener);

    $start = microtime(true);
    $result = $callback();
    $wallTime = (microtime(true) - $start) * 1000;

    // Analyze duplicates
    $sqlCounts = [];
    foreach ($queries as $q) {
        $sql = $q['sql'];
        $sqlCounts[$sql] = ($sqlCounts[$sql] ?? 0) + 1;
    }

    $duplicateCount = 0;
    foreach ($sqlCounts as $sql => $count) {
        if ($count > 1) {
            $duplicateCount += ($count - 1);
        }
    }

    echo "[-] SCENARIO: {$label}\n";
    echo "    Total SQL Queries Executed : " . count($queries) . "\n";
    echo "    Identical / Duplicate Queries: " . $duplicateCount . "\n";
    echo "    Total DB Execution Time    : " . number_format($totalTime, 2) . " ms\n";
    echo "    Wall Clock Execution Time  : " . number_format($wallTime, 2) . " ms\n";

    if ($duplicateCount > 0) {
        echo "    Sample Duplicated Queries (N+1 Pattern):\n";
        $shown = 0;
        foreach ($sqlCounts as $sql => $count) {
            if ($count > 1) {
                $shortSql = strlen($sql) > 90 ? substr($sql, 0, 87) . '...' : $sql;
                echo "      * [Fired {$count} times]: {$shortSql}\n";
                $shown++;
                if ($shown >= 3) break;
            }
        }
    }
    echo "\n";

    return [
        'total_queries' => count($queries),
        'duplicate_queries' => $duplicateCount,
        'db_time' => $totalTime,
        'wall_time' => $wallTime
    ];
}

// Test 1: Simulating Purchase Orders Index Loop without eager loading
profileQueries("1. Purchase Orders Rendering (Simulated Standard Collection Iteration)", function() {
    $pos = PurchaseOrder::take(25)->get();
    $names = [];
    foreach ($pos as $po) {
        // Accessing dynamic relationships inside loops (typical Blade view behavior)
        $vName = $po->vendor ? $po->vendor->name : null;
        $cName = ($po->quotation && $po->quotation->company) ? $po->quotation->company->name : null;
        $pName = ($po->project && $po->project->quotation && $po->project->quotation->company) ? $po->project->quotation->company->name : null;
        $names[] = [$vName, $cName, $pName];
    }
    return count($names);
});

// Test 2: Simulating Purchase Orders with Eager Loading (Optimized Target State)
profileQueries("2. Purchase Orders with Eager Loading (Phase 1 Target State)", function() {
    $pos = PurchaseOrder::with(['vendor', 'quotation.company', 'project.quotation.company'])->take(25)->get();
    $names = [];
    foreach ($pos as $po) {
        $vName = $po->vendor ? $po->vendor->name : null;
        $cName = ($po->quotation && $po->quotation->company) ? $po->quotation->company->name : null;
        $pName = ($po->project && $po->project->quotation && $po->project->quotation->company) ? $po->project->quotation->company->name : null;
        $names[] = [$vName, $cName, $pName];
    }
    return count($names);
});

// Test 3: Project Reports with nested relations
profileQueries("3. Project Reports Iteration (Simulating Dashboard / Report View)", function() {
    $reports = ProjectReport::take(25)->get();
    $data = [];
    foreach ($reports as $report) {
        $projName = $report->project ? $report->project->subject : null;
        $creator = $report->creator ? $report->creator->name : null;
        $data[] = [$projName, $creator];
    }
    return count($data);
});

// Test 4: Payment Bookings Table Rendering
profileQueries("4. Payment Bookings Listing (Simulating Booking Ledger View)", function() {
    $bookings = PaymentBooking::take(25)->get();
    $data = [];
    foreach ($bookings as $b) {
        $creator = $b->creator ? $b->creator->name : null;
        $approvals = $b->approvals ? $b->approvals->count() : 0;
        $data[] = [$creator, $approvals];
    }
    return count($data);
});

echo "======================================================================\n";
echo "PROBE 2 COMPLETE: N+1 QUERY BEHAVIOR EMPIRICALLY QUANTIFIED\n";
echo "======================================================================\n";
