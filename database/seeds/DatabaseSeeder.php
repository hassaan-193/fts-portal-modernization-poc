<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(LpoOutTypeSeeder::class);
        $this->call(InvoiceTypeSeeder::class);
        $this->call(ProjectTypeSeeder::class);
        $this->call(InvoiceBankSeeder::class);
        $this->call(LookupSeeder::class);
        $this->call(BalanceAccountSeeder::class);

        require_once __DIR__ . '/SampleDataSeeder.php';
        $this->call(SampleDataSeeder::class);
    }
}
