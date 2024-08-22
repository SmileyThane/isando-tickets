<?php


namespace App\Repositories;

use App\Company;
use App\Enums\KnowledgeBase\KnowledgeBasePermissionsTypesEnum;
use App\KbArticle;
use App\KbArticleClient;
use App\KbCategory;
use App\KnowledgeBaseType;
use App\Language;
use App\Permission;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class KbRepository
{
    public function getCategoriesTree($typeId)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company
                ->kbCategories()->orderBy('name', 'ASC')->where('type_id', $typeId)->orderBy('name_de', 'ASC')->get()->toTree();
        }
        return $result;
    }

    public function getCategories($typeId, $category_id = null, $search)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->kbCategories()
                ->where('type_id', $typeId)
                ->orderBy('name', 'ASC')
                ->orderBy('name_de', 'ASC');

            if (empty($search)) {
                $result->where('parent_id', $category_id);
            } else {
                $result = $result->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')->orWhere('name_de', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')->orWhere('description_de', 'like', '%' . $search . '%');
                });
            }

            if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
                $result = $result->where('is_draft', '=', false);
            }

            if (Auth::user()->employee->assignedToClients()->exists()) {
                $result = $result->where('is_internal', '=', false);
            }

            $result = $result
                ->withCount('articles')
                ->with(['children'])
                ->get();

            if (!empty($category_id)) {
                $resultCategory = kbCategory::where('id', $category_id)
                    ->withCount('articles')
                    ->with(['children']);

                if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
                    $resultCategory = $resultCategory->where('is_draft', '=', false);
                }

                if (Auth::user()->employee->assignedToClients()->exists()) {
                    $resultCategory = $resultCategory->where('is_internal', '=', false);
                }

                $result = $resultCategory->get()->merge($result);
            }
        }

        return $result;
    }

    public function createCategory($company_id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color, $type_id = null, $is_internal = 0, $is_draft = 0)
    {
        return KbCategory::create(compact('company_id', 'parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'type_id', 'is_internal', 'is_draft'));
    }

    public function updateCategory($id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color, $is_internal, $is_draft)
    {
        return KbCategory::updateOrCreate(compact('id'), compact('parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'is_internal', 'is_draft'));
    }

    public function deleteCategory($id)
    {
        $category = KbCategory::find($id);
        return $category ? $category->delete() : false;
    }

    public function getArticles($request, $typeId, $category_id, $search, $search_in_text = false, $tags = [])
    {
        $articles = KbArticle::query()
            ->select([
                'id',
                'name',
                'name_de',
                'summary',
                'summary_de',
                'featured_color',
                'is_draft',
                'is_internal'
            ])
            ->with(['tags', 'attachments'])
            ->where('type_id', $typeId);


        if (!empty($search)) {
            $articles->where(function ($query) use ($search, $search_in_text, $request) {
                $query->where('name', 'like', '%' . $search . '%')->orWhere('name_de', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')->orWhere('summary_de', 'like', '%' . $search . '%');
                if ($search_in_text) {
                    $query->orWhere(function ($query) use ($search) {
                        $query->orWhere('keywords', 'like', '%' . $search . '%')->orWhere('keywords_de', 'like', '%' . $search . '%')
                            ->orWhere('content', 'like', '%' . $search . '%')->orWhere('content_de', 'like', '%' . $search . '%');
                    });
                }

                if (!empty($request->company_name)) {
                    $query->orWhereHas('clients', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
                }

                if (!empty($request->client_number)) {
                    $query->orWhereHas('clients', function ($query) use ($search) {
                        $query->where('number', 'like', '%' . $search . '%');
                    });
                }

                if (!empty($request->description)) {
                    $query->orWhereHas('clients', function ($query) use ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    });
                }

                if (!empty($request->description)) {
                    $query->orWhereHas('clients', function ($query) use ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    });
                }

                if (!empty($request->email)) {
                    $query->orWhereHas('clients.emails', function ($query) use ($search) {
                        $query->where('email', 'like', '%' . $search . '%');
                    });
                }
            });


        }

        if (!empty($tags)) {
            $articles->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereIn('tags.id', $tags);
            });
        }

        if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
            $articles->where('is_draft', '=', false);
        }

        if (Auth::user()->employee->assignedToClients()->exists()) {
            $articles->where('is_internal', '=', false);
        }

        if ($category_id) {
            $articles->whereHas('categories', function (Builder $query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        } elseif(empty($tags) && empty($search)) {
            $articles->whereDoesntHave('categories');
        }

        return $articles->get();
    }

    public function getAllArticles($typeId)
    {
        $articles = KbArticle::select('id', 'name', 'name_de')->where('type_id', $typeId);
        if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
            $articles->where('is_draft', '=', false);
        }

        if (
            !Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)
            &&
            Auth::user()->employee->assignedToClients()->exists()
        ) {
            $articles->where('is_internal', '=', false);
        }

        return $articles->with('categories')->orderBy('name', 'ASC')->orderBy('name_de', 'ASC')->get();
    }

    public function getArticle($id)
    {
        return KbArticle::with('categories', 'tags', 'attachments', 'next', 'clients')->find($id)->makeVisible(['content', 'content_de']);
    }

    public function createArticle($company_id, $categories, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [], $is_internal = 0, $keywords = null, $keywords_de = null, $featured_color = 'transparent', $next_steps = [], $step_type = 1, $type_id = null, $client_ids = [], $is_draft = 0)
    {
        $owner_id = Auth::user()->employee->id;
        $article = KbArticle::create(compact('company_id', 'name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'is_internal', 'keywords', 'keywords_de', 'featured_color', 'type_id', 'owner_id', 'is_draft'));

        foreach ($categories as $category) {
            $article->categories()->attach($category);
        }

        foreach ($tags as $tag) {
            if (is_object($tag)) {
                $article->tags()->attach($tag->id);
            } else {
                $tag = Tag::create([
                    'name' => $tag,
                    'color' => Tag::randomHexColor()
                ]);

                $article->tags()->attach($tag->id);
            }
        }

        foreach ($next_steps as $i => $step) {
            $stepId = is_object($step) ? $step->id : $step;

            if ($stepId == 0) {
                $date = date('Y-m-d H:i:s');

                $step = KbArticle::create([
                    'company_id' => $company_id,
                    'name' => (Language::find(1))->langMap->kb->new_knowledge_name . ' ' . $date . ' ' . ($i + 1),
                    'name_de' => (Language::find(2))->langMap->kb->new_knowledge_name . ' ' . $date . ' ' . ($i + 1),
                ]);

                $stepId = $step->id;
            }
            $article->next()->attach($stepId, ['relation_type' => $step_type, 'position' => $i + 1]);
        }

        if ($client_ids && count($client_ids) > 0) {
            $article->clients()->sync($client_ids);
        }

        return $article;
    }

    public function updateArticle($request, $id)
    {
        $owner_id = Auth::user()->employee->id;
        $request['owner_id'] = $owner_id;
        $article = KbArticle::query()->find($id);
        if ($article) {
            $article->update($request);

            foreach ($article->categories as $category) {
                $article->categories()->detach($category->id);
            }
            if (isset($request['categories'])) {
                foreach ($request['categories'] as $category) {
                    $article->categories()->attach($category);
                }

            }

            foreach ($article->tags as $tag) {
                $article->tags()->detach($tag->id);
            }

            if (isset($request['tags'])) {
                foreach ($request['tags'] as $tag) {
                    if (is_object($tag)) {
                        $article->tags()->attach($tag->id);
                    } else {
                        $tag = Tag::create([
                            'name' => $tag,
                            'color' => Tag::randomHexColor()
                        ]);

                        $article->tags()->attach($tag->id);
                    }
                }
            }

            foreach ($article->next as $step) {
                $article->next()->detach($step->id);
            }

            if (isset($request['next_steps'])) {
                foreach ($request['next_steps'] as $i => $step) {
                    $stepId = is_object($step) ? $step->id : $step;

                    if ($stepId == 0) {
                        $date = date('Y-m-d H:i:s');

                        $step = KbArticle::create([
                            'company_id' => $article->company_id,
                            'name' => (Language::find(1))->langMap->kb->new_knowledge_name . ' ' . $date . ' ' . ($i + 1),
                            'name_de' => (Language::find(2))->langMap->kb->new_knowledge_name . ' ' . $date . ' ' . ($i + 1),
                        ]);

                        $stepId = $step->id;
                    }
                    $article->next()->attach($stepId, ['relation_type' => $request['step_type'], 'position' => $i + 1]);

                    if ($request['client_ids'] && count($request['client_ids']) > 0) {
                        $article->clients()->sync($request['client_ids']);
                    }
                }
            }
        }

        return $article;
    }

    public function deleteArticle($id)
    {
        KbArticleClient::query()->where('kb_article_id', '=', $id)->delete();
        $article = KbArticle::find($id);
        return $article ? $article->delete() : false;
    }

    /**
     * Find KnowledgeBaseType by alias
     *
     * @param string $alias
     * @return Builder|Model
     */
    public function findByAlias(string $alias)
    {
        return KnowledgeBaseType::query()->where('alias', $alias)->firstOrFail();
    }

    /**
     * Create new KnowledgeBaseType entity
     *
     * @param array $data
     * @return Model|KnowledgeBaseType
     */
    public function create(array $data): Model|KnowledgeBaseType
    {
        $data['alias'] = str_replace(' ', '_', strtolower($data['name']));
        $data['permissions'] = [
            [
                'type' => KnowledgeBasePermissionsTypesEnum::VIEW,
                'value' => Permission::KB_VIEW_ACCESS,
            ],
            [
                'type' => KnowledgeBasePermissionsTypesEnum::CREATE,
                'value' => Permission::KB_CREATE_ACCESS,
            ],
            [
                'type' => KnowledgeBasePermissionsTypesEnum::EDIT,
                'value' => Permission::KB_EDIT_ACCESS,
            ],
            [
                'type' => KnowledgeBasePermissionsTypesEnum::DELETE,
                'value' => Permission::KB_DELETE_ACCESS,
            ],
        ];
        return KnowledgeBaseType::query()->create($data);
    }

    /**
     * Update KnowledgeBaseType entity
     *
     * @param KnowledgeBaseType $knowledgeBaseType
     * @param array $data
     * @return KnowledgeBaseType
     */
    public function update(KnowledgeBaseType $knowledgeBaseType, array $data): KnowledgeBaseType
    {
        return tap($knowledgeBaseType)->update($data);
    }

    /**
     * Delete KnowledgeBaseType entity
     *
     * @param KnowledgeBaseType $knowledgeBaseType
     * @return bool
     */
    public function delete(KnowledgeBaseType $knowledgeBaseType): bool
    {
        return $knowledgeBaseType->delete();
    }

    public function prettifyArticlePdf(string $title, string $content):string
    {
        $styles = '<style>
                        img {max-width: 100%;}
                        * {font-family: Roboto, sans-serif!important; font-size: 12px!important;}
                  </style>';

        return $styles . '<div style="padding: 10px;"> <h1 style="font-size: 24px!important;">'. $title .'</h1>' . $content . '</div>';
    }
}
