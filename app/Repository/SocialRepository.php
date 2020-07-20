<?php


namespace App\Repository;


use App\Address;
use App\Social;
use Throwable;

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

    public function update($id, $type, $value): Address
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
        } catch (Throwable $throwable) {
            return false;
        }
    }
}
