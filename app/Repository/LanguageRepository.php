<?php


namespace App\Repository;


use App\Language;

class LanguageRepository
{

    public function all()
    {
        return Language::select('id', 'name', 'locale')->get();
    }

    public function find($id)
    {
        return Language::find($id);
    }

}
