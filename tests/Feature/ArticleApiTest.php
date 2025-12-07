<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test articles
        $this->createTestArticles();
    }

    /** @test */
    public function it_can_retrieve_paginated_articles(): void
    {
        $response = $this->getJson('/api/v1/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'source' => ['api', 'name'],
                        'author',
                        'title',
                        'description',
                        'url',
                        'image',
                        'content',
                        'published_at',
                        'category',
                        'language',
                    ],
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
            ]);
    }

    /** @test */
    public function it_can_filter_articles_by_title(): void
    {
        $response = $this->getJson('/api/v1/articles?filter[title]=AI');

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertStringContainsStringIgnoringCase('AI', $article['title']);
        }
    }

    /** @test */
    public function it_can_filter_articles_by_source(): void
    {
        $response = $this->getJson('/api/v1/articles?filter[source_api]=newsapi');

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertEquals('newsapi', $article['source']['api']);
        }
    }

    /** @test */
    public function it_can_filter_articles_by_category(): void
    {
        $response = $this->getJson('/api/v1/articles?filter[category]=technology');

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertEquals('technology', $article['category']);
        }
    }

    /** @test */
    public function it_can_filter_articles_by_author(): void
    {
        $response = $this->getJson('/api/v1/articles?filter[author]=John Doe');

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertStringContainsString('John Doe', $article['author']);
        }
    }

    /** @test */
    public function it_can_sort_articles_by_published_date_descending(): void
    {
        $response = $this->getJson('/api/v1/articles?sort=-published_at');

        $response->assertStatus(200);

        $articles = $response->json('data');

        // Verify descending order
        for ($i = 0; $i < count($articles) - 1; $i++) {
            $current = strtotime($articles[$i]['published_at']);
            $next = strtotime($articles[$i + 1]['published_at']);
            $this->assertGreaterThanOrEqual($next, $current);
        }
    }

    /** @test */
    public function it_can_sort_articles_by_published_date_ascending(): void
    {
        $response = $this->getJson('/api/v1/articles?sort=published_at');

        $response->assertStatus(200);

        $articles = $response->json('data');

        // Verify ascending order
        for ($i = 0; $i < count($articles) - 1; $i++) {
            $current = strtotime($articles[$i]['published_at']);
            $next = strtotime($articles[$i + 1]['published_at']);
            $this->assertLessThanOrEqual($next, $current);
        }
    }

    /** @test */
    public function it_can_sort_articles_by_author(): void
    {
        $response = $this->getJson('/api/v1/articles?sort=author');

        $response->assertStatus(200);

        $articles = $response->json('data');

        // Verify alphabetical order
        for ($i = 0; $i < count($articles) - 1; $i++) {
            if ($articles[$i]['author'] && $articles[$i + 1]['author']) {
                $this->assertLessThanOrEqual(
                    $articles[$i + 1]['author'],
                    $articles[$i]['author']
                );
            }
        }
    }

    /** @test */
    public function it_can_combine_filters_and_sorting(): void
    {
        $response = $this->getJson(
            '/api/v1/articles?filter[category]=technology&filter[source_api]=newsapi&sort=-published_at'
        );

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertEquals('technology', $article['category']);
            $this->assertEquals('newsapi', $article['source']['api']);
        }
    }

    /** @test */
    public function it_can_paginate_results(): void
    {
        $response = $this->getJson('/api/v1/articles?per_page=5&page=1');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_validates_per_page_parameter(): void
    {
        $response = $this->getJson('/api/v1/articles?per_page=invalid');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);

        $response = $this->getJson('/api/v1/articles?per_page=10');
        $response->assertStatus(200);

        $response = $this->getJson('/api/v1/articles?per_page=150');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_empty_array_when_no_articles_match_filter(): void
    {
        $response = $this->getJson('/api/v1/articles?filter[title]=NonExistentArticle12345');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function it_can_filter_by_multiple_fields_simultaneously(): void
    {
        $response = $this->getJson(
            '/api/v1/articles?filter[category]=technology&filter[author]=John Doe&filter[source_api]=newsapi'
        );

        $response->assertStatus(200);

        $articles = $response->json('data');

        foreach ($articles as $article) {
            $this->assertEquals('technology', $article['category']);
            $this->assertStringContainsString('John Doe', $article['author']);
            $this->assertEquals('newsapi', $article['source']['api']);
        }
    }

    // Helper method to create test data
    private function createTestArticles(): void
    {
        // NewsAPI articles
        Article::factory()->count(10)->create([
            'source_api' => 'newsapi',
            'source_name' => 'TechCrunch',
            'category' => 'technology',
            'author' => 'John Doe',
        ]);

        Article::factory()->count(8)->create([
            'source_api' => 'newsapi',
            'source_name' => 'BBC News',
            'category' => 'business',
            'author' => 'Jane Smith',
        ]);

        // GNews articles
        Article::factory()->count(7)->create([
            'source_api' => 'gnews',
            'source_name' => 'CNN',
            'category' => 'technology',
            'author' => 'Mike Johnson',
        ]);

        // TheNewsAPI articles
        Article::factory()->count(5)->create([
            'source_api' => 'thenewsapi',
            'source_name' => 'Reuters',
            'category' => 'science',
            'author' => 'Sarah Williams',
        ]);
    }
}
