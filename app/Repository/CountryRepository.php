<?php


namespace App\Repository;


use App\Country;
use App\CompanyCountry;
use Illuminate\Support\Facades\Auth;


class CountryRepository
{

    public function getAllCountries()
    {
        return Country::all();

    }
    public function getCountriesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyCountries = CompanyCountry::where('company_id', $companyId)->pluck('id');
        return Country::whereIn('id', $companyCountries)->get();
    }

    public function getCompanyCountryIds($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyCountry::where('company_id', $companyId)->pluck('country_id');
    }

    public function createCompanyCountry($countryId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        return CompanyCountry::firstOrCreate([
            'country_id' => $countryId,
            'company_id' => $companyId
        ]);
    }

    public function deleteCompanyCountry($countryId, $companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        try {
            return (bool) CompanyCountry::where('country_id', $countryId)->where('company_id', $companyId)->delete();
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
