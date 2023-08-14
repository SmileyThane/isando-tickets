<?php


namespace App\Http\Controllers\API;

use App\Exceptions\Customs\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\KnowledgeBase\CreateKnowledgeBaseTypeRequest;
use App\Http\Requests\KnowledgeBase\UpdateKnowledgeBaseTypeRequest;
use App\KbArticle;
use App\KnowledgeBaseType;
use App\Permission;
use App\Repositories\FileRepository;
use App\Repositories\KbRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KbController extends Controller
{
    const IMPORTANCE = [3 => 'low', 2 => 'medium', 1 => 'high'];
    protected $kbRepo;
    protected $fileRepo;

    public function __construct(KbRepository $kbRepository, FileRepository $fileRepository)
    {
        $this->kbRepo = $kbRepository;
        $this->fileRepo = $fileRepository;
    }

    public function listCategories(Request $request)
    {
        return self::showResponse(true, $this->kbRepo->getCategories($this->getTypeByAlias($request->type), $request->category_id, $request->search));
    }

    private function getTypeByAlias($alias)
    {
        $KBType = KnowledgeBaseType::query()->where('alias', $alias)->first();
        return $KBType ? $KBType->id : 1;
    }

    public function listTypes(Request $request): JsonResponse
    {
        return self::showResponse(true, KnowledgeBaseType::all());
    }

    public function categoriesTree(Request $request)
    {
        return self::showResponse(true, $this->kbRepo->getCategoriesTree($this->getTypeByAlias($request->type)));
    }

    public function addCategory(Request $request)
    {
        return self::showResponse(true, $this->kbRepo->createCategory(
            Auth::user()->employee->companyData->id,
            $request->parent_id,
            $request->name ?? '',
            $request->name_de,
            $request->description,
            $request->description_de,
            $request->icon ?? 'mdi-help',
            $request->icon_color,
            $this->getTypeByAlias($request->type),
            (int)$request->is_internal,
            (int)$request->is_draft
        ));
    }

    public function editCategory(Request $request, $id)
    {
        return self::showResponse(true, $this->kbRepo->updateCategory(
            $id,
            $request->parent_id,
            $request->name ?? '',
            $request->name_de,
            $request->description,
            $request->description_de,
            $request->icon ?? 'mdi-help',
            $request->icon_color,
            $request->is_internal,
            (int)$request->is_draft
        ));
    }

    public function deleteCategory(Request $request, $id)
    {
        return self::showResponse($this->kbRepo->deleteCategory($id));
    }

    public function listArticles(Request $request)
    {
        return self::showResponse(true, $this->kbRepo->getArticles($this->getTypeByAlias($request->type), $request->category_id, $request->search, $request->search_in_text, $request->tags));
    }

    public function allArticles(Request $request)
    {
        return self::showResponse(true, $this->kbRepo->getAllArticles($this->getTypeByAlias($request->type)));
    }


    public function getArticle(Request $request, $id)
    {
        return self::showResponse(true, $this->kbRepo->getArticle($id));
    }

    public function addArticle(Request $request)
    {

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
            $request->featured_color,
            $request->next ? json_decode($request->next) : [],
            $request->step_type,
            $this->getTypeByAlias($request->type),
            $request->client_ids,
            (int)$request->is_draft
        );

        if ($request->has('files')) {
            foreach ($request['files'] as $i => $file) {
                $this->fileRepo->store($file, $article->id, KbArticle::class, $request['file_infos'][$i]);

                $article = $article->refresh();
            }
        }

        return self::showResponse(true, $article);
    }

    public function editArticle(Request $request, $id)
    {
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
            $request->featured_color,
            $request->next ? json_decode($request->next) : [],
            $request->step_type,
            $request->client_ids,
            [],
            (int)$request->is_draft
        );


        if ($request->has('files')) {
            foreach ($request['files'] as $i => $file) {
                $this->fileRepo->store($file, $article->id, KbArticle::class, json_decode($request['file_infos'][$i]));

                $article = $article->refresh();
            }
        }

        return self::showResponse(true, $article);
    }

    public function deleteArticle(Request $request, $id)
    {
        return self::showResponse($this->kbRepo->deleteArticle($id));
    }

    public function importanceList()
    {
        return self::showResponse(true, self::IMPORTANCE);
    }

    /**
     * Create new KnowledgeBaseType entity
     *
     * @param CreateKnowledgeBaseTypeRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function create(CreateKnowledgeBaseTypeRequest $request): JsonResponse
    {
        throw_unless(
            Auth::user()->employee->hasPermissionId(Permission::KB_CREATE_ACCESS),
            CustomException::class,
        );

        return self::showResponse(true, $this->kbRepo->create($request->validated()));
    }

    /**
     * Update KnowledgeBaseType entity
     *
     * @param UpdateKnowledgeBaseTypeRequest $request
     * @param KnowledgeBaseType $knowledgeBaseType
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(UpdateKnowledgeBaseTypeRequest $request, KnowledgeBaseType $knowledgeBaseType): JsonResponse
    {
        throw_unless(
            Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS),
            CustomException::class,
        );

        return self::showResponse(true, $this->kbRepo->update($knowledgeBaseType, $request->validated()));
    }

    /**
     * Delete KnowledgeBaseType entity
     *
     * @param KnowledgeBaseType $knowledgeBaseType
     * @return JsonResponse
     * @throws \Throwable
     */
    public function delete(KnowledgeBaseType $knowledgeBaseType): JsonResponse
    {
        throw_unless(
            Auth::user()->employee->hasPermissionId(Permission::KB_DELETE_ACCESS),
            CustomException::class,
        );

        return self::showResponse($this->kbRepo->delete($knowledgeBaseType));
    }
}
