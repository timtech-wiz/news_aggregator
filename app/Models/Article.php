<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'source_api',
        'source_name',
        'author',
        'title',
        'description',
        'url',
        'url_to_image',
        'content',
        'published_at',
        'category',
        'language',
        'metadata',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function scopeBySource(Builder $query, string $source): Builder
    {
        return $query->where('source_api', $source);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByAuthor(Builder $query, string $author): Builder
    {
        return $query->where('author', 'like', "%{$author}%");
    }

    public function scopePublishedBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('published_at', [$from, $to]);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereRaw(
            "MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)",
            [$search]
        );
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }
}
