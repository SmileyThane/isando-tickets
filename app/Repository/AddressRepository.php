<?php


namespace App\Repository;


use App\Address;
use App\AddressType;
use App\CompanyAddressType;
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

    public function createType($name, $icon): AddressType
    {
        return AddressType::firstOrCreate([
                'name' => $name,
                'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $icon): AddressType
    {
        $type = AddressType::findOrFail($id);
        $type->update([
                'name' => $name,
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

    public function getAllTypes()
    {
        return AddressType::all();

    }
    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyTypes = CompanyAddressType::where('company_id', $companyId)->pluck('id');
        return AddressType::whereIn('id', $companyTypes)->get();
    }

    public function getCompanyTypeIds($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyAddressType::where('company_id', $companyId)->pluck('address_type_id');
    }

    public function createCompanyType($addressTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyAddressType::firstOrCreate([
            'address_type_id' => $addressTypeId,
            'company_id' => $companyId
        ]);
    }

    public function deleteCompanyType($addressTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        try {
            return (bool) CompanyAddressType::where('address_type_id', $addressTypeId)->where('company_id', $companyId)->delete();
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
