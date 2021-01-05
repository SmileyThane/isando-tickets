<?php


namespace App\Repository;


use App\CompanyCountry;
use App\Country;
use Illuminate\Support\Facades\Auth;
use Throwable;


class CountryRepository
{

    public function getAllCountries()
    {
        return Country::orderBy('name', 'ASC')->get();
    }

    public function getCountriesInCompanyContext($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;
        $companyCountries = CompanyCountry::where('company_id', $companyId)->pluck('country_id');
        return Country::whereIn('id', $companyCountries)->orderBy('name', 'ASC')->get();
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
            return (bool)CompanyCountry::where('country_id', $countryId)->where('company_id', $companyId)->delete();
        } catch (Throwable $throwable) {
            return false;
        }
    }
}
