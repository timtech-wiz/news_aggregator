<?php

namespace App\Services;

use App\Contracts\{
    NewsAggregatorInterface,
    ArticleRepositoryInterface,
    NewsApiClientInterface,
    FetchLoggerInterface
};
use Illuminate\Support\Facades\Log;

class NewsAggregatorService implements NewsAggregatorInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private FetchLoggerInterface $logger,
        private iterable $newsClients
    ) {}

    public function aggregate(array $params = []): array
    {
        $results = [
            'total_fetched' => 0,
            'total_saved' => 0,
            'total_duplicates' => 0,
            'sources' => [],
        ];

        foreach ($this->newsClients as $client) {
            if (!$client instanceof NewsApiClientInterface || !$client->isAvailable()) {
                continue;
            }

            $sourceResult = $this->fetchFromSource($client, $params);

            $results['total_fetched'] += $sourceResult['fetched'];
            $results['total_saved'] += $sourceResult['saved'];
            $results['total_duplicates'] += $sourceResult['duplicates'];
            $results['sources'][$client->getSourceName()] = $sourceResult;
        }

        return $results;
    }

    private function fetchFromSource(NewsApiClientInterface $client, array $params): array
    {
        $sourceName = $client->getSourceName();
        $logId = $this->logger->startFetch($sourceName);

        try {
            Log::info("Fetching from {$sourceName}");

            $articles = $client->fetchArticles($params);
            $stats = $this->saveArticles($articles);

            $this->logger->completeFetch($logId, $stats);

            Log::info("Completed {$sourceName}", $stats);

            return $stats;
        } catch (\Exception $e) {
            $this->logger->failFetch($logId, $e->getMessage());
            Log::error("Failed {$sourceName}: {$e->getMessage()}");

            return ['fetched' => 0, 'saved' => 0, 'duplicates' => 0];
        }
    }

    private function saveArticles(array $articles): array
    {
        $saved = 0;
        $duplicates = 0;

        foreach ($articles as $articleDTO) {
            if (!$this->articleRepository->existsByUrl($articleDTO->url)) {
                $this->articleRepository->create($articleDTO->toArray());
                $saved++;
            } else {
                $duplicates++;
            }
        }

        return [
            'fetched' => count($articles),
            'saved' => $saved,
            'duplicates' => $duplicates,
        ];
    }
}
