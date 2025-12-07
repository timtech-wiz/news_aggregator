<?php

namespace App\Services\NewsApi;

use App\Contracts\NewsApiClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractNewsClient implements NewsApiClientInterface
{
    protected string $apiKey;

    protected string $baseUrl;

    protected int $timeout = 30;

    abstract protected function getApiKey(): string;

    abstract protected function getBaseUrl(): string;

    abstract protected function buildRequestParams(array $params): array;

    abstract protected function transformResponse(array $data): array;

    public function __construct()
    {
        $this->apiKey = $this->getApiKey();
        $this->baseUrl = $this->getBaseUrl();
    }

    public function fetchArticles(array $params = []): array
    {
        if (! $this->isAvailable()) {
            Log::warning("{$this->getSourceName()} is not available (missing API key)");

            return [];
        }

        try {
            $requestParams = $this->buildRequestParams($params);
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl, $requestParams);

            if ($response->successful()) {
                return $this->transformResponse($response->json());
            }

            Log::error("{$this->getSourceName()} fetch failed", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("{$this->getSourceName()} exception: {$e->getMessage()}");

            return [];
        }
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }
}
