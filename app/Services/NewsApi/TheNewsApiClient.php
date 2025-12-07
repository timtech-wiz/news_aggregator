<?php

namespace App\Services\NewsApi;

use App\DataTransferObjects\ArticleData;

class TheNewsApiClient extends AbstractNewsClient
{
    protected function getApiKey(): string
    {
        return config('services.thenewsapi.key', '');
    }

    protected function getBaseUrl(): string
    {
        return config('services.thenewsapi.uri');
    }

    protected function buildRequestParams(array $params): array
    {
        $defaultParams = [
            'api_token' => $this->apiKey,
            'language' => 'en',
            'limit' => 100,
        ];

        return array_merge($defaultParams, $params);
    }

    protected function transformResponse(array $data): array
    {
        $articles = $data['data'] ?? [];

        return array_map(function ($article) {
            return new ArticleData(
                sourceApi: 'thenewsapi',
                sourceName: $article['source'] ?? 'Unknown',
                author: null,
                title: $article['title'] ?? '',
                description: $article['description'] ?? null,
                url: $article['url'] ?? '',
                urlToImage: $article['image_url'] ?? null,
                content: $article['snippet'] ?? null,
                publishedAt: $article['published_at'] ?? now()->toISOString(),
                category: $article['categories'][0] ?? null,
                language: $article['locale'] ?? 'en',
                metadata: ['categories' => $article['categories'] ?? []]
            );
        }, $articles);
    }

    public function getSourceName(): string
    {
        return 'thenewsapi';
    }
}
