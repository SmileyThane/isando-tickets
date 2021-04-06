<?php

namespace App\Repositories;

use App\InternalBilling;

class InternalBillingRepository
{

    public function find(int $id)
    {
        return InternalBilling::find($id);
    }

    public function create($entityId, $entityType, $cost, $currencyId)
    {
        return InternalBilling::create([
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'cost' => $cost,
            'currency_id' => $currencyId
        ]);
    }

    public function update($id, $cost, $currencyId)
    {
        $internalBilling = InternalBilling::find($id);
        if ($internalBilling) {
            $internalBilling->update([
                'cost' => $cost,
                'currency_id' => $currencyId
            ]);
        }

        return $internalBilling;
    }

    public function delete($id): bool
    {
        $internalBilling = InternalBilling::find($id);

        if ($internalBilling) {
            $internalBilling->delete();
            return true;
        }

        return false;
    }
}
