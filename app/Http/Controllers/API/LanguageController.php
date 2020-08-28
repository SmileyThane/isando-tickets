<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Repository\LanguageRepository;

class LanguageController extends Controller
{
    private $langRepo;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->langRepo = $languageRepository;
    }

    public function all()
    {
        return self::showResponse(true, $this->langRepo->all());
    }

    public function find($id)
    {
        return self::showResponse(true, $this->langRepo->find($id));
    }
}
