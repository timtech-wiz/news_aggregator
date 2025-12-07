<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface ArticleRepositoryInterface
{
    public function create(array $data): ?object;

    public function findByUrl(string $url): ?object;

    public function existsByUrl(string $url): bool;

    public function getQueryBuilder(): Builder;

    public function deleteOlderThan(int $days): int;
}
