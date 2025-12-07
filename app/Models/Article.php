<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

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

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereRaw(
            'MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)',
            [$search]
        );
    }
}
