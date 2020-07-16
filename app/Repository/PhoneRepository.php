<?php


namespace App\Repository;


use App\Phone;

class PhoneRepository
{

    public function create($entityId, $entityType, $phoneValue, $phoneType): Phone
    {
        $phone = new Phone();
        $phone->entity_id = $entityId;
        $phone->entity_type = $entityType;
        $phone->phone = $phoneValue;
        $phone->phone_type = $phoneType;
        $phone->save();
        return $phone;
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
}
