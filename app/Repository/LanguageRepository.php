<?php


namespace App\Repository;


use App\Language;
use App\CompanyLanguage;
use Illuminate\Support\Facades\Auth;


class LanguageRepository
{

    public function getAllLanguages()
    {
        return Language::select(['id', 'name', 'locale'])->orderBy('id', 'ASC')->get();

    }
    public function getLanguagesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyLangs = CompanyLanguage::where('company_id', $companyId)->pluck('language_id');
        return Language::select(['id', 'name', 'locale'])->whereIn('id', $companyLangs)->orderBy('id', 'ASC')->get();
    }

    public function getCompanyLanguageIds($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyLanguage::where('company_id', $companyId)->pluck('language_id');
    }

    public function createCompanyLanguage($languageId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyLanguage::firstOrCreate([
            'language_id' => $languageId,
            'company_id' => $companyId
        ]);
    }

    public function deleteCompanyLanguage($languageId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        try {
            return (bool) CompanyLanguage::where('language_id', $languageId)->where('company_id', $companyId)->delete();
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function find($id)
    {
        return Language::find($id);
    }
}
