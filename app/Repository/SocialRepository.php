<?php


namespace App\Repository;

use App\Company;
use App\Social;
use App\SocialType;
use Illuminate\Support\Facades\Auth;

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
        $social->social_link = $value;
        $social->social_type = $type;
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

    public function createType($name, $name_de, $icon, $companyId = null): SocialType
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        return SocialType::firstOrCreate([
            'entity_type' => Company::class,
            'entity_id' => $companyId,
            'name' => $name,
            'name_de' => $name_de,
            'icon' => $icon
        ]);
    }

    public function updateType($id, $name, $name_de, $icon): SocialType
    {
        $type = SocialType::findOrFail($id);
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
            SocialType::where('id', $id)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return SocialType::where('entity_type', Company::class)->where('entity_id', $companyId)->get();
    }
}
