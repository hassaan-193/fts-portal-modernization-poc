<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users: " . App\User::count() . "\n";
echo "Companies: " . App\Models\Company::count() . "\n";
echo "Vendors: " . App\Models\Vendor::count() . "\n";
echo "Projects: " . App\Models\Project::count() . "\n";
echo "Inquiries: " . App\Models\Inquiry::count() . "\n";
echo "PurchaseOrders: " . App\Models\PurchaseOrder::count() . "\n";
echo "ProjectReports: " . App\Models\ProjectReport::count() . "\n";
echo "PaymentBookings: " . App\Models\PaymentBooking::count() . "\n";
