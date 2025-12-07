<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function create(array $data): ?object;
    public function findByUrl(string $url): ?object;
    public function existsByUrl(string $url): bool;
    public function search(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function deleteOlderThan(int $days): int;
}
