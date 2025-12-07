<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchArticlesRequest;
use App\Http\Resources\ArticleCollection;
use App\Contracts\ArticleRepositoryInterface;
use App\DataTransferObjects\SearchFilterData;
use App\DTOs\SearchFilterDTO;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleRepositoryInterface $repository
    ) {}

    /**
     * Retrieve articles based on search queries and filtering criteria
     *
     * @param SearchArticlesRequest $request
     * @return ArticleCollection
     */
    public function __invoke(SearchArticlesRequest $request): ArticleCollection
    {
        $filters = SearchFilterData::fromRequest($request->validated());

        $articles = $this->repository->search(
            (array) $filters,
            $filters->perPage
        );

        return new ArticleCollection($articles);
    }
}
