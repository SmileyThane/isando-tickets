<?php


namespace App\Repositories;


use App\KbArticle;
use App\KbRepository;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KbRepository
{
    public function getCategoriesTree($categoryId, $search) {}
    public function createCategory($companyId, $parentId, $name, $nameDe, $description, $descriptionDe, $icon) {} 
    public function updateCategory($id, $name, $nameDe, $description, $descriptionDe, $icon) {} 
    public function deleteCategory($id) {}

getArticles($categoryId, $search) {}
createArticle($companyId, $categoryId, $name, $nameDe, $content, $contentDe) {}
updateArticle($id, $categoryId, $name, $nameDe, $content, $contentDe) {}
deleteArticle($id) {}
processFiles($article, $request);

}
