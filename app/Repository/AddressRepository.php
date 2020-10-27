<?php


namespace App\Repository;

use App\Address;
use App\AddressType;
use App\Company;
use Illuminate\Support\Facades\Auth;


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

    public function createType($name, $name_de, $icon, $companyId = null): AddressType
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        return AddressType::firstOrCreate([
            'entity_type' => Company::class,
            'entity_id' => $companyId,
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $name_de, $icon): AddressType
    {
        $type = AddressType::findOrFail($id);
        $type->update([
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
        $type->save();
        return $type;
    }

    public function deleteType($id): ?bool
    {
        try {
            AddressType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return AddressType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
    }
}
