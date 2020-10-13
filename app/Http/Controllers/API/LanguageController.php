<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Repository\LanguageRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LanguageController extends Controller
{
    private $langRepo;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->langRepo = $languageRepository;
    }

    public function list()
    {
        return self::showResponse(true, $this->langRepo->getAllLanguagesInCompanyContext());
    }

    public function find($id = null)
    {
        return self::showResponse(true, $this->langRepo->find($id ?? Auth::user()->language_id));
    }

    public function all()
    {
        return self::showResponse(true, $this->langRepo->getAllLanguages());
    }

    public function getCompanyLanguages()
    {
        return self::showResponse(true, $this->langRepo->getCompanyLanguageIds());
    }

    public function addCompanyLanguage(Request $request)
    {
        $lang = $this->langRepo->createCompanyLanguage($request->language_id, $request->company_id);
        return self::showResponse(true, $lang);
    }
    public function deleteCompanyLanguage(Request $request)
    {
        return self::showResponse($this->langRepo->deleteCompanyLanguage($request->language_id, $request->company_id));
    }
}
