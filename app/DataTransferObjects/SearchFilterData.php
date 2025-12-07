<?php

namespace App\DataTransferObjects;

class SearchFilterData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?string $source = null,
        public readonly ?string $category = null,
        public readonly ?string $author = null,
        public readonly ?string $from = null,
        public readonly ?string $to = null,
        public readonly ?string $language = null,
        public readonly int $perPage = 20
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            query: $data['q'] ?? null,
            source: $data['source'] ?? null,
            category: $data['category'] ?? null,
            author: $data['author'] ?? null,
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            language: $data['language'] ?? null,
            perPage: (int) ($data['per_page'] ?? 20)
        );
    }
}
