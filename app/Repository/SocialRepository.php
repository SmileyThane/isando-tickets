<?php


namespace App\Repository;


use App\Social;
use App\SocialType;

class SocialRepository
{

    public function create($entityId, $entityType, $socialLink, $socialType): Social
    {
        return Social::firstOrCreate(
            [
                'entity_id' => $entityId,
                'entity_type' => $entityType,
                'social_link' => $socialLink,
                'social_type' => $socialType
            ]
        );
    }

    public function update($id, $type, $value): Social
    {
        $social = Social::find($id);
        $social->socialLink = $value;
        $social->socialType = $type;
        $social->save();
        return $social;
    }

    public function delete($id): ?bool
    {
        try {
            Social::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }


    public function createType($name, $icon): SocialType
    {
        return SocialType::firstOrCreate([
                'name' => $name,
                'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $icon): SocialType
    {
        $type = SocialType::findOrFail($id);
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
            SocialType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
