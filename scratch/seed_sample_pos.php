<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\Company;
use App\Models\Quotation;
use App\User;
use Carbon\Carbon;

echo "Seeding 10 Sample Purchase Orders for N+1 Profiling...\n";

$admin = User::first();
$adminId = $admin ? $admin->id : 1;

$vendors = Vendor::all();
if ($vendors->isEmpty()) {
    $v1 = Vendor::create(['name' => 'Hikvision Middle East', 'email' => 'sales@hikvision.ae']);
    $v2 = Vendor::create(['name' => 'Schneider Electric FZE', 'email' => 'orders@schneider.ae']);
    $vendors = collect([$v1, $v2]);
}

$project = Project::first();
$projectId = $project ? $project->id : null;

// Ensure quotation exists for company linking
$company = Company::first();
$quotation = Quotation::first();
if (!$quotation && $company) {
    $quotation = Quotation::create([
        'ref_no' => 'QUO-2026-001',
        'company_id' => $company->id,
        'created_by' => $adminId,
        'date' => Carbon::now()->toDateString(),
        'total' => 50000
    ]);
}
$quotationId = $quotation ? $quotation->id : null;

for ($i = 1; $i <= 10; $i++) {
    $vendor = $vendors[$i % count($vendors)];
    PurchaseOrder::firstOrCreate(
        ['request_number' => sprintf("PO-SAMPLE-%03d", $i)],
        [
            'request_type' => 'project',
            'quotation_id' => $quotationId,
            'project_id' => $projectId,
            'lpout_vendor_id' => $vendor->id,
            'lpout_name' => 'Supply of Hardware Batch #' . $i,
            'total_amount' => 2500.00 * $i,
            'status' => ($i % 2 == 0) ? 'Approved' : 'Admin Approved',
            'department_status' => 'Approved',
            'date' => Carbon::now()->subDays($i)->toDateString(),
            'created_by' => $adminId,
            'items' => [
                ['item' => 'Network Cable Cat6', 'qty' => 5, 'price' => 250],
                ['item' => 'Patch Panel 24 Port', 'qty' => 2, 'price' => 350],
            ]
        ]
    );
}

echo "SUCCESS: Purchase Orders Count is now: " . PurchaseOrder::count() . "\n";
