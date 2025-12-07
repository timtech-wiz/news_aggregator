<?php

namespace App\DataTransferObjects;

class ArticleData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $sourceApi,
        public readonly string $sourceName,
        public readonly ?string $author,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $url,
        public readonly ?string $urlToImage,
        public readonly ?string $content,
        public readonly string $publishedAt,
        public readonly ?string $category,
        public readonly string $language,
        public readonly ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return [
            'source_api' => $this->sourceApi,
            'source_name' => $this->sourceName,
            'author' => $this->author,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'url_to_image' => $this->urlToImage,
            'content' => $this->content,
            'published_at' => $this->publishedAt,
            'category' => $this->category,
            'language' => $this->language,
            'metadata' => $this->metadata,
        ];
    }
}
