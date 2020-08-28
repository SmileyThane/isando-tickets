<?php


namespace App\Repository;


use App\Language;

class LanguageRepository
{

    public function all()
    {
        return Language::select('id', 'name', 'short_code')->get();
    }

    public function find($id)
    {
        return Language::find($id);
    }

}
