<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Repositories\BaseRepository;

class PurchaseOrderRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'po_number',
        'project_id',
        'status',
        'department_status',
        'date'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return PurchaseOrder::class;
    }

    public function create($input)
    {
        $input['po_number'] = $this->generatePoNumber();
        $input['created_by'] = auth()->id();
        $input['status'] = 'Pending';
        $input['department_status'] = 'Pending';

        if (isset($input['items']) && is_array($input['items'])) {
            $total = 0;
            foreach ($input['items'] as $item) {
                $total += ($item['quantity'] ?? 0) * ($item['cost'] ?? 0);
            }
            $input['total_amount'] = $total;
        }

        return parent::create($input);
    }

    public function update($input, $id)
    {
        if (isset($input['items']) && is_array($input['items'])) {
            $total = 0;
            foreach ($input['items'] as $item) {
                $total += ($item['quantity'] ?? 0) * ($item['cost'] ?? 0);
            }
            $input['total_amount'] = $total;
        }

        return parent::update($input, $id);
    }

    private function generatePoNumber()
    {
        $latest = PurchaseOrder::latest('id')->first();
        $number = ($latest ? $latest->id : 0) + 1;
        return 'PO-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
