<?php

namespace App\Http\Controllers\Api\v1;

use App\Filters\CategoryFilter;
use App\Filters\DateFilter;
use App\Filters\HasFilters;
use App\Filters\KeywordFilter;
use App\Filters\PersonalizedFeedFilter;
use App\Filters\SourceFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticlesController extends Controller
{
    use HasFilters;

    public function index()
    {
        $filters = [
            KeywordFilter::class,
            DateFilter::class,
            SourceFilter::class,
            CategoryFilter::class,
            PersonalizedFeedFilter::class,
        ];

        $query = Article::query()->with(['category']);
        $query = $this->applyFilters($query, $filters);
        $articles = $query->paginate();

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }
}
