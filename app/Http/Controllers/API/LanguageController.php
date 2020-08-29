<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Repository\LanguageRepository;
use Illuminate\Support\Facades\Auth;

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

    public function find($id = null)
    {
        return self::showResponse(true, $this->langRepo->find($id ?? Auth::user()->language_id));
    }
}
