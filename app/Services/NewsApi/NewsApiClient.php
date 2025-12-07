<?php

namespace App\Services\NewsApi;

use App\DataTransferObjects\ArticleData;

class NewsApiClient extends AbstractNewsClient
{
    protected function getApiKey(): string
    {
        return config('services.newsapi.key', '');
    }

    protected function getBaseUrl(): string
    {
        return 'https://newsapi.org/v2/everything';
    }

    protected function buildRequestParams(array $params): array
    {
        return array_merge([
            'apiKey' => $this->apiKey,
            'language' => 'en',
            'pageSize' => 100,
            'sortBy' => 'publishedAt',
        ], $params);
    }

    protected function transformResponse(array $data): array
    {
        $articles = $data['articles'] ?? [];

        return array_map(function ($article) {
            return new ArticleData(
                sourceApi: 'newsapi',
                sourceName: $article['source']['name'] ?? 'Unknown',
                author: $article['author'] ?? null,
                title: $article['title'] ?? '',
                description: $article['description'] ?? null,
                url: $article['url'] ?? '',
                urlToImage: $article['urlToImage'] ?? null,
                content: $article['content'] ?? null,
                publishedAt: $article['publishedAt'] ?? now()->toISOString(),
                category: null,
                language: 'en',
                metadata: ['source_id' => $article['source']['id'] ?? null]
            );
        }, $articles);
    }

    public function getSourceName(): string
    {
        return 'newsapi';
    }
}
