<?php


namespace App\Repositories;

use App\Company;
use App\KbArticle;
use App\KbCategory;
use App\Language;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KbRepository
{
    public function getCategoriesTree()
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->kbCategories()->orderBy('name', 'ASC')->orderBy('name_de', 'ASC')->get()->toTree();
        }
        return $result;
    }

    public function getCategories($category_id = null, $search)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->kbCategories()->orderBy('name', 'ASC')->orderBy('name_de', 'ASC');

            $result = empty($category_id) ? $result->whereNull('parent_id') : $result->where('parent_id', $category_id);

            if (!empty($search)) {
                $result = $result->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')->orWhere('name_de', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')->orWhere('description_de', 'like', '%' . $search . '%');
                });
            }
            $result = $result->get();

            if (!empty($category_id)) {
                $result = kbCategory::where('id', $category_id)->get()->merge($result);
            }
        }
        return $result;
    }

    public function createCategory($company_id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color)
    {
        return KbCategory::create(compact('company_id', 'parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color'));
    }

    public function updateCategory($id, $parent_id, $name, $name_de, $description, $description_de, $icon, $icon_color)
    {
        return KbCategory::updateOrCreate(compact('id'), compact('parent_id', 'name', 'name_de', 'description', 'description_de', 'icon', 'icon_color'));
    }

    public function deleteCategory($id)
    {
        $category = KbCategory::find($id);
        return $category ? $category->delete() : false;
    }

    public function getArticles($category_id, $search, $search_in_text = false, $tags = [])
    {
        $articles = KbArticle::with('tags', 'attachments')->orderBy('name', 'ASC')->orderBy('name_de', 'ASC');
        if ($category_id) {
            $articles = $articles->whereHas('categories', function (Builder $query) use ($category_id) {
                $query->where('category_id', $category_id);
            });
        } else {
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

        return $articles->get();
    }

    public function getAllArticles()
    {
        return KbArticle::select('id', 'name', 'name_de')->with('categories')->orderBy('name', 'ASC')->orderBy('name_de', 'ASC')->get();
    }

    public function getArticle($id)
    {
        return KbArticle::with('categories', 'tags', 'attachments', 'next')->find($id);
    }

    public function createArticle($company_id, $categories, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [], $is_internal = 0, $keywords = null, $keywords_de = null, $featured_color = 'transparent', $next_steps = [], $step_type = 1)
    {
        $article = KbArticle::create(compact('company_id', 'name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'is_internal', 'keywords', 'keywords_de', 'featured_color'));

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

        return $article;
    }

    public function updateArticle($id, $categories, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [], $is_internal = 0, $keywords = null, $keywords_de = null, $featured_color = 'transparent', $next_steps = [], $step_type = 1)
    {
        $article = KbArticle::updateOrCreate(compact('id'), compact('name', 'name_de', 'summary', 'summary_de', 'content', 'content_de', 'is_internal', 'keywords', 'keywords_de', 'featured_color'));

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
        }

        return $article;
    }

    public function deleteArticle($id)
    {
        $article = KbArticle::find($id);
        return $article ? $article->delete() : false;
    }
}
