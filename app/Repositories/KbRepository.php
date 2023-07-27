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
                ->where('parent_id', $category_id)
                ->orderBy('name', 'ASC')
                ->orderBy('name_de', 'ASC');

            if (!empty($search)) {
                $result = $result->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')->orWhere('name_de', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')->orWhere('description_de', 'like', '%' . $search . '%');
                });
            }
            $result = $result
                ->withCount('articles')
                ->with(['children'])
                ->get()
                ->append(['has_sub_categories']);

            if (!empty($category_id)) {
                $result = kbCategory::where('id', $category_id)
                    ->withCount('articles')
                    ->with(['children'])
                    ->get()
                    ->append(['has_sub_categories'])
                    ->merge($result);
            }
        }
        return $result;
    }

    public function createCategory($company_id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color, $type_id = null, $is_internal = 0)
    {
        return KbCategory::create(compact('company_id', 'parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'type_id', 'is_internal'));
    }

    public function updateCategory($id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color, $is_internal)
    {
        return KbCategory::updateOrCreate(compact('id'), compact('parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color', 'is_internal'));
    }

    public function deleteCategory($id)
    {
        $category = KbCategory::find($id);
        return $category ? $category->delete() : false;
    }

    public function getArticles($typeId, $category_id, $search, $search_in_text = false, $tags = [])
    {
        $articles = KbArticle::with('tags', 'attachments')->where('type_id', $typeId)->orderBy('name', 'ASC')->orderBy('name_de', 'ASC');

        if ($category_id) {
            $articles = $articles->whereHas('categories', function (Builder $query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        } elseif (empty($search) && empty($tags)) {
            $articles = $articles->whereDoesntHave('categories');
        }

        if (!empty($search)) {
            $articles = $articles->where(function ($query) use ($search, $search_in_text) {
                $query->where('name', 'like', '%' . $search . '%')->orWhere('name_de', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')->orWhere('summary_de', 'like', '%' . $search . '%');
            });

            if ($search_in_text) {
                $articles = $articles->where(function ($query) use ($search) {
                    $query->orWhere('keywords', 'like', '%' . $search . '%')->orWhere('keywords_de', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%')->orWhere('content_de', 'like', '%' . $search . '%');
                });
            }
        }

        if (!empty($tags)) {
            $articles->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereIn('tags.id', $tags);
            });
        }

        if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
            $articles->where('is_draft', '=', false);
        }

        return $articles->get();
    }

    public function getAllArticles($typeId)
    {
        $articles = KbArticle::select('id', 'name', 'name_de')->where('type_id', $typeId);
        if (!Auth::user()->employee->hasPermissionId(Permission::KB_EDIT_ACCESS)) {
            $articles->where('is_draft', '=', false);
        }

        return $articles->with('categories')->orderBy('name', 'ASC')->orderBy('name_de', 'ASC')->get();
    }

    public function getArticle($id)
    {
        return KbArticle::with('categories', 'tags', 'attachments', 'next', 'clients')->find($id);
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

    public function updateArticle($id, $categories, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [], $is_internal = 0, $keywords = null, $keywords_de = null, $featured_color = 'transparent', $next_steps = [], $step_type = 1, $approved_at = null, $client_ids = [], $is_draft = 0)
    {
        $owner_id = Auth::user()->employee->id;
        $article = KbArticle::updateOrCreate(compact('id'), compact('name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'is_internal', 'keywords', 'keywords_de', 'featured_color', 'approved_at', 'owner_id', 'is_draft'));

        foreach ($article->categories as $category) {
            $article->categories()->detach($category->id);
        }
        foreach ($categories as $category) {
            $article->categories()->attach($category);
        }

        foreach ($article->tags as $tag) {
            $article->tags()->detach($tag->id);
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

        foreach ($article->next as $step) {
            $article->next()->detach($step->id);
        }

        foreach ($next_steps as $i => $step) {
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
            $article->next()->attach($stepId, ['relation_type' => $step_type, 'position' => $i + 1]);

            if ($client_ids && count($client_ids) > 0) {
                $article->clients()->sync($client_ids);
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
}
