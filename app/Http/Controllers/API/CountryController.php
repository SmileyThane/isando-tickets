<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;


class CountryController extends Controller
{
    private $repo;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->repo = $countryRepository;
    }

    public function getCountries()
    {
        return self::showResponse(true, $this->repo->getCountriesInCompanyContext());
    }

    public function getAllCountries()
    {
        return self::showResponse(true, $this->repo->getAllCountries());
    }

    public function getCompanyCountries()
    {
        return self::showResponse(true, $this->repo->getCompanyCountryIds());
    }

    public function addCompanyCountry(Request $request)
    {
        $country = $this->repo->createCompanyCountry($request->country_id, $request->company_id);
        return self::showResponse(true, $country);
    }

    public function deleteCompanyCountry(Request $request, $id)
    {
        return self::showResponse($this->repo->deleteCompanyCountry($id, $request->company_id));
    }
}
