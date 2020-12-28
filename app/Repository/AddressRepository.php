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
        return Address::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'address_type' => $addressType,
                'street' => $addressValue['street'],
                'street2' => $addressValue['street2'],
                'street3' => $addressValue['street3'],
                'city' => $addressValue['city'],
                'postal_code' => $addressValue['postal_code'],
                'country_id' => $addressValue['country_id']
            ]
        );
    }

    public function update($id, $addressType, $addressValue): Address
    {
        $address = Address::find($id);
        $address->update([
            'address_type' => $addressType,
            'street' => $addressValue['street'],
            'street2' => $addressValue['street2'],
            'street3' => $addressValue['street3'],
            'city' => $addressValue['city'],
            'postal_code' => $addressValue['postal_code'],
            'country_id' => $addressValue['country_id']
        ]);
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
