<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminatech\Balance\Facades\Balance;

class RecalculateBalances extends Command
{
    protected $signature = 'balance:recalculate';
    protected $description = 'Recalculate and update all account balances from transaction data.';

    public function handle()
    {
        $accounts = DB::table('balance_accounts')->get();

        foreach ($accounts as $account) {
            $calculatedBalance = Balance::calculateBalance($account->id);

            DB::table('balance_accounts')
                ->where('id', $account->id)
                ->update(['balance' => $calculatedBalance]);

            $this->info("Account ID {$account->id} updated: balance = {$calculatedBalance}");
        }

        $this->info('All balances recalculated successfully.');
    }
}