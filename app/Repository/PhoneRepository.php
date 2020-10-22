<?php


namespace App\Repository;


use App\CompanyPhoneType;
use App\Phone;
use App\PhoneType;
use Illuminate\Support\Facades\Auth;

class PhoneRepository
{

    public function create($entityId, $entityType, $phoneValue, $phoneType): Phone
    {
        return Phone::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'phone' => $phoneValue,
                'phone_type' => $phoneType
            ]
        );
    }

    public function update($id, $type, $value): Phone
    {
        $phone = Phone::find($id);
        $phone->phone = $value;
        $phone->phone_type = $type;
        $phone->save();
        return $phone;
    }

    public function delete($id): ?bool
    {
        try {
            Phone::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function createType($name, $icon): PhoneType
    {
        return PhoneType::firstOrCreate([
                'name' => $name,
                'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $icon): PhoneType
    {
        $type = PhoneType::findOrFail($id);
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
            PhoneType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getAllTypes()
    {
        return PhoneType::all();

    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyTypes = CompanyPhoneType::where('company_id', $companyId)->pluck('phone_type_id');
        return PhoneType::whereIn('id', $companyTypes)->get();
    }

    public function getCompanyTypeIds($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyPhoneType::where('company_id', $companyId)->pluck('phone_type_id');
    }

    public function createCompanyType($phoneTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyPhoneType::firstOrCreate([
            'phone_type_id' => $phoneTypeId,
            'company_id' => $companyId
        ]);
    }

    public function deleteCompanyType($phoneTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        try {
            return (bool) CompanyPhoneType::where('phone_type_id', $phoneTypeId)->where('company_id', $companyId)->delete();
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
