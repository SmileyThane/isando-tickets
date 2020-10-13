<?php


namespace App\Repository;


use App\Language;
use App\CompanyLanguage;
use Illuminate\Support\Facades\Auth;


class LanguageRepository
{

    public function getAllLanguages()
    {
        return Language::select('id', 'name', 'locale')->get();

    }
    public function getAllLanguagesInCompanyContext()
    {
        $companyLangs = CompanyLanguage::where('company_id', Auth::user()->employee->companyData->id)->pluck('id');
        return Language::select('id', 'name', 'locale')->whereIn('id', $companyLangs)->get();
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
            CompanyLanguage::where('language_id', $languageId)->where('company_id', $companyId)->delete();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function find($id)
    {
        return Language::find($id);
    }
}
