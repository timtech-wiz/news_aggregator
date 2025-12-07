<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $data = [
            'source_api' => 'newsapi',
            'source_name' => 'TechCrunch',
            'title' => 'Test Article',
            'url' => 'https://example.com/test',
            'published_at' => now(),
        ];

        $article = $this->repository->create($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
    }

    /** @test */
    public function it_can_find_article_by_url()
    {
        $article = Article::factory()->create([
            'url' => 'https://example.com/unique-url',
        ]);

        $found = $this->repository->findByUrl('https://example.com/unique-url');

        $this->assertNotNull($found);
        $this->assertEquals($article->id, $found->id);
    }

    /** @test */
    public function it_returns_null_when_article_not_found_by_url()
    {
        $found = $this->repository->findByUrl('https://example.com/non-existent');

        $this->assertNull($found);
    }

    /** @test */
    public function it_can_check_if_article_exists_by_url()
    {
        Article::factory()->create([
            'url' => 'https://example.com/exists',
        ]);

        $this->assertTrue($this->repository->existsByUrl('https://example.com/exists'));
        $this->assertFalse($this->repository->existsByUrl('https://example.com/not-exists'));
    }

    /** @test */
    public function it_can_delete_old_articles()
    {
        Article::factory()->count(5)->create([
            'published_at' => now()->subDays(60),
        ]);

        Article::factory()->count(10)->create([
            'published_at' => now()->subDays(10),
        ]);

        $deleted = $this->repository->deleteOlderThan(30);

        $this->assertEquals(5, $deleted);
        $this->assertEquals(10, Article::count());
    }
}
