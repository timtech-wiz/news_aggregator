<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArticleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchArticlesRequest;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleRepositoryInterface $repository
    ) {}

    /**
     * Retrieve articles based on search queries and filtering criteria
     */
    public function __invoke(SearchArticlesRequest $request): ArticleCollection
    {
        $articles = QueryBuilder::for(Article::class)
            ->allowedFilters([
                'title',
                'content',
                'author',
                'source_name',
                'source_api',
                'category',
                'language',
                AllowedFilter::exact('source_api'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                'published_at',
                'source_name',
                'author',
                'title',
            ])
            ->defaultSort('-published_at')
            ->paginate($request->input('per_page', 20));

        return new ArticleCollection($articles);
    }
}
