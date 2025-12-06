<?php

namespace App\Services\NewsApi;

use App\DataTransferObjects\ArticleData;

class GNewsClient extends AbstractNewsClient
{
    protected function getApiKey(): string
    {
        return config('services.gnews.key', '');
    }

    protected function getBaseUrl(): string
    {
        return config('services.gnews.uri');
    }

    protected function buildRequestParams(array $params): array
    {
        $defaultParams = [
            'apikey' => $this->apiKey,
            'lang' => 'en',
            'max' => 100,
        ];

        if (empty($params['q'])) {
            $defaultParams['q'] = 'technology OR business OR world news';
        }

        return array_merge($defaultParams, $params);
    }

    protected function transformResponse(array $data): array
    {
        $articles = $data['articles'] ?? [];

        return array_map(function ($article) {
            return new ArticleData(
                sourceApi: 'gnews',
                sourceName: $article['source']['name'] ?? 'Unknown',
                author: null,
                title: $article['title'] ?? '',
                description: $article['description'] ?? null,
                url: $article['url'] ?? '',
                urlToImage: $article['image'] ?? null,
                content: $article['content'] ?? null,
                publishedAt: $article['publishedAt'] ?? now()->toISOString(),
                category: null,
                language: 'en',
                metadata: ['source_url' => $article['source']['url'] ?? null]
            );
        }, $articles);
    }

    public function getSourceName(): string
    {
        return 'gnews';
    }
}
