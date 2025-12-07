<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleanArticlesCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_old_articles()
    {
        Article::factory()->count(5)->create([
            'published_at' => now()->subDays(60),
        ]);

        Article::factory()->count(10)->create([
            'published_at' => now()->subDays(10),
        ]);

        $this->artisan('news:clean', ['--days' => 30])
            ->assertSuccessful();

        $this->assertEquals(10, Article::count());
    }

    /** @test */
    public function it_keeps_articles_within_retention_period()
    {
        Article::factory()->count(15)->create([
            'published_at' => now()->subDays(20),
        ]);

        $this->artisan('news:clean', ['--days' => 30])
            ->assertSuccessful();

        $this->assertEquals(15, Article::count());
    }

    /** @test */
    public function it_uses_default_retention_period()
    {
        Article::factory()->count(5)->create([
            'published_at' => now()->subDays(40),
        ]);

        Article::factory()->count(5)->create([
            'published_at' => now()->subDays(20),
        ]);

        $this->artisan('news:clean')
            ->assertSuccessful();

        $this->assertEquals(5, Article::count());
    }
}
