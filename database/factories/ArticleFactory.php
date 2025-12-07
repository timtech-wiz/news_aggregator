<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'source_api' => $this->faker->randomElement(['newsapi', 'gnews', 'thenewsapi']),
            'source_name' => $this->faker->company(),
            'author' => $this->faker->name(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'url' => $this->faker->unique()->url(),
            'url_to_image' => $this->faker->imageUrl(),
            'content' => $this->faker->paragraphs(3, true),
            'published_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'category' => $this->faker->randomElement(['technology', 'business', 'science', 'sports']),
            'language' => 'en',
            'metadata' => [
                'source_id' => $this->faker->uuid(),
            ],
        ];
    }
}
