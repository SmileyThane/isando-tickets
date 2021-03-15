<?php


namespace App\Repositories;

use App\Company;
use App\Phone;
use App\PhoneType;
use Illuminate\Support\Facades\Auth;
use Throwable;

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
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function createType($name, $name_de, $icon, $companyId = null): PhoneType
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        return PhoneType::firstOrCreate([
            'entity_type' => Company::class,
            'entity_id' => $companyId,
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $name_de, $icon): PhoneType
    {
        $type = PhoneType::findOrFail($id);
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
            PhoneType::where('id', $id)->delete();
            return true;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return PhoneType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
    }
}
