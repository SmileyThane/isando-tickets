<?php


namespace App\Repository;


use App\Address;

class AddressRepository
{

    public function create($entityId, $entityType, $addressValue, $addressType): Address
    {
        return Address::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'address' => $addressValue,
                'address_type' => $addressType
            ]
        );
    }

    public function update($id, $type, $value): Address
    {
        $address = Address::find($id);
        $address->address = $value;
        $address->address_type = $type;
        $address->save();
        return $address;
    }

    public function delete($id): ?bool
    {
        try {
            Address::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
