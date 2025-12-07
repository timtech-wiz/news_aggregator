<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => [
                'api' => $this->source_api,
                'name' => $this->source_name,
            ],
            'author' => $this->author,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'image' => $this->url_to_image,
            'content' => $this->content,
            'published_at' => $this->published_at->toISOString(),
            'category' => $this->category,
            'language' => $this->language,
            'metadata' => $this->metadata,
        ];
    }
}
