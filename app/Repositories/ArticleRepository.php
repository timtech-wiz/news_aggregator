<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Article $model)
    {}

    public function create(array $data): ?object
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function findByUrl(string $url): ?object
    {
        return $this->model->where('url', $url)->first();
    }

    public function existsByUrl(string $url): bool
    {
        return $this->model->where('url', $url)->exists();
    }

    public function search(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['query'])) {
            $query->search($filters['query']);
        }

        if (!empty($filters['source'])) {
            $query->bySource($filters['source']);
        }

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (!empty($filters['author'])) {
            $query->byAuthor($filters['author']);
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->publishedBetween($filters['from'], $filters['to']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        return $query->latest('published_at')->paginate($perPage);
    }

    public function deleteOlderThan(int $days): int
    {
        return $this->model
            ->where('published_at', '<', now()->subDays($days))
            ->delete();
    }
}
