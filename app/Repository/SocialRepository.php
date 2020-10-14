<?php


namespace App\Repository;


use App\Social;
use App\SocialType;
use App\CompanySocialType;
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

   public function getAllTypes()
    {
        return SocialType::all();

    }
    public function getTypesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyTypes = CompanySocialType::where('company_id', $companyId)->pluck('id');
        return SocialType::whereIn('id', $companyTypes)->get();
    }

    public function getCompanyTypeIds($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanySocialType::where('company_id', $companyId)->pluck('social_type_id');
    }

    public function createCompanyType($socialTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanySocialType::firstOrCreate([
            'social_type_id' => $socialTypeId,
            'company_id' => $companyId
        ]);
    }

    public function deleteCompanyType($socialTypeId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        try {
            return (bool) CompanySocialType::where('social_type_id', $socialTypeId)->where('company_id', $companyId)->delete();
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
