<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import and process invoice data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = app()->call([\App\Http\Controllers\HomeController::class, 'importData']);
        if (is_array($result)) {
            $this->info("✅ Processed invoices: {$result['processed']}");
            $this->info("⏭️ Missing Transaction invoices: {$result['no_transaction']}");
            $this->info("⏭️ Cheque Not Cleared invoices: {$result['cheque_not_cleared']}");
        } else {
            $this->error("❌ Error: " . $result->getMessage());
        }    }
}
