<?php

use App\Models\Account;
use Illuminate\Database\Seeder;

class BalanceAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('balance_accounts')->truncate();

        // Core bookkeeping accounts required by invoice/receipt logic
        Account::create([
            'type' => 'Sales',
            'code' => 'sales',
            'account_type' => 'Income'
        ]);

        Account::create([
            'type' => 'Accounts Receivable',
            'code' => 'account_receivable',
            'account_type' => 'Accounts Receivable'
        ]);        

        Account::create([
            'type' => 'VAT Output',
            'code' => 'vat_out',
            'account_type' => 'Other Current Liability'
        ]);

        Account::create([
            'type' => 'VAT Input',
            'code' => 'vat_in',
            'account_type' => 'Other Current Liability'
        ]);

        Account::create([
            'type' => 'Account Payable',
            'code' => 'account_payable',
            'account_type' => 'Accounts Payable'
        ]);

        Account::create([
            'type' => 'Material Expense',
            'code' => 'material_expense',
            'account_type' => 'Expense'
        ]);

        // Other app-specific accounts
        Account::create(['type' => 'Project']);
        Account::create(['type' => 'PettyCash']);
        Account::create(['type' => 'Advance']);
    }
}
