<?php

namespace App\Contracts;

interface NewsApiClientInterface
{
    public function fetchArticles(array $params = []): array;
    public function getSourceName(): string;
    public function isAvailable(): bool;
}
