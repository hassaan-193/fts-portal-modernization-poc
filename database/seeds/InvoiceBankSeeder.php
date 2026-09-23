<?php

use App\Models\InvoiceBank;
use Illuminate\Database\Seeder;

class InvoiceBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_banks')->truncate();

        InvoiceBank::create([
            'beneficary_account_name' => 'Fire Technical Services LLC',
            'bank_name' => 'RAK Bank FTS',
            'bank_branch' => 'Abu Dhabi Tourist Club Branch',
            'account_no' => '0000000000001',
            'account_currency'	=> 'AED',
            'iban_no' => 'AE000000000000000000001',
            'swift_code' => 'NRAKAEAK'
        ]);

        InvoiceBank::create([
            'beneficary_account_name' => 'Fire Technical Services LLC',
            'bank_name' => 'ADCB FTS',
            'bank_branch' => 'RAK Branch, Ras Al Khaimah',
            'account_no' => '0000000000002',
            'account_currency'	=> 'AED',
            'iban_no' => 'AE000000000000000000002',
            'swift_code' => 'ADCBAEAA'
        ]);

        InvoiceBank::create([
            'beneficary_account_name' => 'EXPERTS INTERNATIONAL CONSULTANCY FZ LLC',
            'bank_name' => 'ADCB EIC',
            'bank_branch' => 'RAK Branch, Ras Al Khaimah',
            'account_no' => '0000000000003',
            'account_currency'	=> 'AED',
            'iban_no' => 'AE000000000000000000003',
            'swift_code' => ''
        ]);

        InvoiceBank::create([
            'beneficary_account_name' => 'FTSITS LLC',
            'bank_name' => 'Mashreq NEOBiz',
            'bank_branch' => 'RAK Branch, Ras Al Khaimah',
            'account_no' => '0000000000004',
            'account_currency'	=> 'AED',
            'iban_no' => 'AE000000000000000000004',
            'swift_code' => 'BOMLAEAD'
        ]);
    }

}
