<?php

use App\Models\Lookup;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lookups')->truncate();

        // Transaction entries
        $data = [
            [
                'name' => 'Receipt',
                'tag' => 'transaction_type'
            ],
            [
                'name' => 'Payment',
                'tag' => 'transaction_type'
            ],
            [
                'name' => 'Cash',
                'tag' => 'transaction_payment_type'
            ],
            [
                'name' => 'Cheque',
                'tag' => 'transaction_payment_type'
            ],
            [
                'name' => 'Credit Card',
                'tag' => 'transaction_payment_type'
            ],
            [
                'name' => 'Bank Trasfer',
                'tag' => 'transaction_payment_type'
            ],
        ];
        Lookup::insert($data);

        // Quotation types
        $data = [
            [
                'name' => 'Annual Maintenance Contract',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'Gas AMC',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'Maintenance',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'Supply & Installation',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'Supply',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'Authority Approval',
                'tag' => 'quotation_type'
            ],
            [
                'name' => 'CCTV',
                'tag' => 'quotation_type'
            ]
        ];
        Lookup::insert($data);

    }

}
