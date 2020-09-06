<?php


namespace App\Repository;


use App\Address;

class AddressRepository
{

    public function create($entityId, $entityType, $addressValue, $addressType): Address
    {
        $addressValue = $this->modifyAddressValues($addressValue);
        return Address::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'address' => $addressValue['address'],
                'address_line_2' => $addressValue['address_line_2'],
                'address_line_3' => $addressValue['address_line_3'],
                'address_type' => $addressType
            ]
        );
    }

    private function modifyAddressValues($valuesArray)
    {
        $separator = $valuesArray['postal_code'] && $valuesArray['postal_code'] !== '' ? ', ' : '';
        $valuesArray['address'] .= $separator . $valuesArray['postal_code'];
        $separator = $valuesArray['city'] && $valuesArray['city'] !== '' &&
        $valuesArray['country'] && $valuesArray['country'] !== '' ? ', ' : '';
        $valuesArray['address_line_3'] = $valuesArray['city'] . $separator . $valuesArray['country'];
        return $valuesArray;
    }

    public function update($id, $type, $value): Address
    {
        $address = Address::find($id);
        $address->address = $value['address'];
        $address->address_line_2 = $value['address_line_2'];
        $address->address_line_3 = $value['address_line_3'];
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
