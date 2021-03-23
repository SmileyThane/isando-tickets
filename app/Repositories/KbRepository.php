<?php


namespace App\Repositories;


use App\KbArticle;
use App\KbCategory;
use App\File;
use App\Tag;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KbRepository
{
    public function getCategoriesTree($category_id = null, $search) {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->kbCategories()->orderBy('name', 'ASC')->orderBy('name_de', 'ASC');

            if (!empty($search)) {
                $result = $result->where('name', 'like', $search.'%') ->orWhere('name_de', 'like', $search.'%')
                    ->orWhere('description', 'like', $search.'%') ->orWhere('description_de', 'like', $search.'%');
            }
            $result = $result->get()->toTree();
        }
        return $result;
    }

    public function getCategoriesFlat($companyId = null)
    {
        $companyId = $companyId ?? Auth::user()->employee->companyData->id;

        $result = [];
        $company = Company::find($companyId);
        if ($company) {
            $result = $company->kbCategories()->orderBy('name', 'ASC')->orderBy('name_de', 'ASC');

            if (!empty($search)) {
                $result = $result->where('name', 'like', $search.'%') ->orWhere('name_de', 'like', $search.'%')
                    ->orWhere('description', 'like', $search.'%') ->orWhere('description_de', 'like', $search.'%');
            }
            $result = $result->get();
        }
        return $result;
    }

    public function createCategory($company_id, $parent_id, $name, $name_de, $description, $description_de, $icon) {
        return KbCategory::create(compact($company_id, $parent_id, $name, $name_de, $description, $description_de, $icon));
    }
    public function updateCategory($id, $parent_id, $name, $name_de, $description, $description_de, $icon) {
        return KbCategory::firstOrUpdate($id, compact($parent_id, $name, $name_de, $description, $description_de, $icon));
    }
    public function deleteCategory($id) {
        return KbCategory::delete($id);
    }

    public function getArticles($category_id, $search) {
        return KbArticle::where('category_id',$category_id)->where(function($query) use ($search) {
            $query->where('name', 'like', $search.'%')->orWhere('name_de', 'like', $search.'%')
                ->orWhere('summary', 'like', $search.'%')->orWhere('summary_de', 'like', $search.'%');
        })->get();
    }

    public function getArticle($id) {
        return KbArticle::with('category', 'tags', 'attachments')->find($id);
    }

    public function createArticle($company_id, $category_id, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [], $files = []) {
        $article = KbArticle::create(compact($company_id, $category_id, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags, $files));

        foreach ($tags as $tag) {
            $article->tags()->attach($tag['id']);
        }

        foreach ($files as $file) {
            $article->attachments()->attach($file['id']);
        }

        return $article;
    }

    public function updateArticle($id, $category_id, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags, $files) {
        $article = KbArticle::firstOrCreate($id, compact($category_id, $name, $name_de, $summary, $summary_de, $content, $content_de, $tags = [] , $files) = []);

        foreach ($article->tags as $tag) {
            $article->tags()->detach($tag->id);
        }

        foreach ($tags as $tag) {
            $article->tags()->attach($tag['id']);
        }

        foreach ($files as $file) {
            $article->attachments()->attach($file['id']);
        }

        return $article;
    }

    public function deleteArticle($id) {
        return KbArticle::delete($id);
    }
}
