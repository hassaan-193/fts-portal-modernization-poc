<?php

use App\Models\InvoiceType;
use Illuminate\Database\Seeder;

class InvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('invoice_types')->truncate();

        // create types
        InvoiceType::create(['name' => 'Proforma']);
        InvoiceType::create(['name' => 'Tax']);
    }

}
