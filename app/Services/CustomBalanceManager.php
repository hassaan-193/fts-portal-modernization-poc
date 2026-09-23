<?php

namespace App\Services;

use Illuminatech\Balance\BalanceDb;
use App\Models\Transaction;

class CustomBalanceManager extends BalanceDb
{
    public function __construct(\Illuminate\Database\ConnectionInterface $db)
    {
        parent::__construct($db);

        $this->accountTable = config('balance.accountTable');
        $this->transactionTable = config('balance.transactionTable');
        $this->extraAccountLinkAttribute = config('balance.extraAccountLinkAttribute');
        $this->dataAttribute = config('balance.dataAttribute');
        $this->accountBalanceAttribute = config('balance.accountBalanceAttribute'); // ✅ this is the key one
    }

    protected function createTransaction($attributes)
    {
        $referenceType = $attributes['reference_type'] ?? null;
        $referenceId = $attributes['reference_id'] ?? null;

        unset($attributes['reference_type'], $attributes['reference_id']);

        // Serialize 'data' for keys not in table columns
        $attributes = $this->serializeAttributes(
            $attributes,
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->transactionTable)
        );

        // Now assign reference values explicitly
        $attributes['reference_type'] = $referenceType;
        $attributes['reference_id'] = $referenceId;

        return $this->getConnection()->table($this->transactionTable)->insertGetId($attributes);
    }
}

