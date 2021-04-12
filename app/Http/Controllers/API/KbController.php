<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\KbArticle;
use App\Repositories\FileRepository;
use App\Repositories\KbRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KbController extends Controller
{
    protected $kbRepo;
    protected $fileRepo;

    public function __construct(KbRepository $kbRepository, FileRepository $fileRepository)
    {
        $this->kbRepo = $kbRepository;
        $this->fileRepo = $fileRepository;
    }

    public function listCategories(Request $request) {
        return self::showResponse(true, $this->kbRepo->getCategories($request->category_id, $request->search));
    }

    public function categoriesTree(Request $request) {
        return self::showResponse(true, $this->kbRepo->getCategoriesTree());
    }

    public function addCategory(Request $request) {
        return self::showResponse(true, $this->kbRepo->createCategory(
            Auth::user()->employee->companyData->id,
            $request->parent_id,
            $request->name ?? '',
            $request->name_de,
            $request->description,
            $request->description_de,
            $request->icon ?? 'mdi-help',
            $request->icon_color
        ));
    }

    public function editCategory(Request $request, $id) {
        return self::showResponse(true, $this->kbRepo->updateCategory(
            $id,
            $request->parent_id,
            $request->name ?? '',
            $request->name_de,
            $request->description,
            $request->description_de,
            $request->icon ?? 'mdi-help',
            $request->icon_color
        ));
    }

    public function deleteCategory(Request $request, $id) {
        return self::showResponse($this->kbRepo->deleteCategory($id));
    }

    public function listArticles(Request $request) {
        return self::showResponse(true, $this->kbRepo->getArticles($request->category_id, $request->search, $request->search_in_text, $request->tags));
    }

    public function allArticles(Request $request) {
        return self::showResponse(true, $this->kbRepo->getAllArticles());
    }


    public function getArticle(Request $request, $id) {
        return self::showResponse(true, $this->kbRepo->getArticle($id));
    }

    public function addArticle(Request $request) {

        $article = $this->kbRepo->createArticle(
            Auth::user()->employee->companyData->id,
            $request->categories ? json_decode($request->categories) : [],
            $request->name ?? '',
            $request->name_de,
            $request->summary,
            $request->summary_de,
            $request->input('content'),
            $request->content_de,
            $request->tags ? json_decode($request->tags) : [],
            $request->is_internal ? 1 : 0,
            $request->keywords,
            $request->keywords_de,
            $request->featured_color
        );

        if ($request->has('files')) {
            foreach ($request['files'] as $i => $file) {
                $this->fileRepo->store($file, $article->id, KbArticle::class, $request['file_infos'][$i]);

                $article = $article->refresh();
            }
        }

        return self::showResponse(true, $article);
    }

    public function editArticle(Request $request, $id) {
        $article = $this->kbRepo->updateArticle(
            $id,
            $request->categories ? json_decode($request->categories) : [],
      $request->name ?? '',
            $request->name_de,
            $request->summary,
            $request->summary_de,
            $request->input('content'),
            $request->content_de,
            $request->tags ? json_decode($request->tags) : [],
            $request->is_internal ? 1 : 0,
            $request->keywords,
            $request->keywords_de,
            $request->featured_color
        );


        if ($request->has('files')) {
            foreach ($request['files'] as $i => $file) {
                $this->fileRepo->store($file, $article->id, KbArticle::class, json_decode($request['file_infos'][$i]));

                $article = $article->refresh();
            }
        }

        return self::showResponse(true, $article);
    }

    public function deleteArticle(Request $request, $id) {
        return self::showResponse($this->kbRepo->deleteArticle($id));
    }

}
