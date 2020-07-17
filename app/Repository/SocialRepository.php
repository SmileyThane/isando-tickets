<?php


namespace App\Repository;


use App\Address;
use App\Social;
use Throwable;

class SocialRepository
{

    public function create($entityId, $entityType, $socialLink, $socialType): Social
    {
        $social = new Social();
        $social->entity_id = $entityId;
        $social->entity_type = $entityType;
        $social->social_link = $socialLink;
        $social->social_type = $socialType;
        $social->save();
        return $social;
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
