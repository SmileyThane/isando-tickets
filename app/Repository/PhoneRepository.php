<?php


namespace App\Repository;


use App\Phone;
use App\PhoneType;

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

}
