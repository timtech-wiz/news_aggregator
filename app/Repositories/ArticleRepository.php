<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(private Article $model) {}

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function findByUrl(string $url): ?object
    {
        return $this->model->where('url', $url)->first();
    }

    public function existsByUrl(string $url): bool
    {
        return $this->model->where('url', $url)->exists();
    }

    public function getQueryBuilder(): Builder
    {
        return $this->model->query();
    }

    public function deleteOlderThan(int $days): int
    {
        return $this->model
            ->where('published_at', '<', now()->subDays($days))
            ->delete();
    }
}
