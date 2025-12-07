<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchArticlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'source' => 'nullable|string|in:newsapi,gnews,thenewsapi',
            'category' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'language' => 'nullable|string|size:2',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'q.string' => 'Search query must be a valid string',
            'q.max' => 'Search query cannot exceed 255 characters',
            'source.in' => 'Source must be one of: newsapi, gnews, thenewsapi',
            'from.date' => 'From date must be a valid date',
            'to.date' => 'To date must be a valid date',
            'to.after_or_equal' => 'To date must be after or equal to from date',
            'per_page.min' => 'Per page must be at least 1',
            'per_page.max' => 'Per page cannot exceed 100',
        ];
    }
}
