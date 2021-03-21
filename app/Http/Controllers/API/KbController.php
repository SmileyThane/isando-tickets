<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\KbRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $kbRepo;

    public function __construct(KbRepository $kbRepository)
    {
        $this->kbRepo = $kbRepository;
    }

    public function listCategories(Request $request) { 
        return self::showResponse(true, $this->kbRepo->getCategoriesTree($request->category_id, $request->search));
    }
    public function addCategory(Request $request) {
        return self::showResponse(true, $this->kbRepo->createCategory(
            Auth::user()->employee->companyData->id            
            $request->parent_id,
            $request->name,
            $request->name_de,
            $request->descripton,
            $request->descripton_de,
            $request->icon
        ));
    }
    public function editCategory(Request $request, $id) {
        return self::showResponse(true, $this->kbRepo->updateCategory(
            $id, 
            $request->parent_id,
            $request->name,
            $request->name_de,
            $request->descripton,
            $request->descripton_de,
            $request->icon
        ));
    }
    public function deleteCategory(Request $request, $id) {
        return self::showResponse($this->kbRepo->deleteCategory($id));
    }

    public function listArticles(Request $request) {
        return self::showResponse(true, $this->kbRepo->getArticles($request->category_id, $request->search));
    }
    public function addArticle(Request $request) {

        $article = $this->kbRepo->createArticle(
            Auth::user()->employee->companyData->id            
            $request->category_id,
            $request->name,
            $request->name_de,
            $request->content,
            $request->content_de
            $request->tags
        );

        $this->kbRepo->processFiles($article, $request);

        return self::showResponse(true, $article);
    }
    public function editArticle(Request $request, $id) {
        $article = $this->kbRepo->updateArticle(
            $id, 
            $request->category_id,
            $request->name,
            $request->name_de,
            $request->content,
            $request->content_de,
            $request->tags
        );

        $this->kbRepo->processFiles($article, $request);

        return self::showResponse(true, $article);
    }
    public function deleteArticle(Request $request, $id) {
        return self::showResponse($this->kbRepo->deleteArticle($id));
    }

    public function deleteFile(Request $request, $id,) {
        return self::showResponse($this->kbRepo->deleteFile($id));
    }
}
