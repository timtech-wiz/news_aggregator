<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\FetchLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchNewsCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_news_articles()
    {
        // Mock API responses
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch')
            ->assertSuccessful();

        // Verify articles were saved
        $this->assertDatabaseHas('articles', [
            'source_api' => 'newsapi',
        ]);

        $this->assertDatabaseHas('articles', [
            'source_api' => 'gnews',
        ]);

        $this->assertDatabaseHas('articles', [
            'source_api' => 'thenewsapi',
        ]);
    }

    /** @test */
    public function it_creates_fetch_logs()
    {
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch')
            ->assertSuccessful();

        // Verify fetch logs were created
        $this->assertDatabaseHas('fetch_logs', [
            'source_api' => 'newsapi',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('fetch_logs', [
            'source_api' => 'gnews',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('fetch_logs', [
            'source_api' => 'thenewsapi',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function it_does_not_create_duplicate_articles()
    {
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch')->assertSuccessful();
        $firstCount = Article::count();

        $this->artisan('news:fetch')->assertSuccessful();
        $secondCount = Article::count();

        $this->assertEquals($firstCount, $secondCount);
    }

    /** @test */
    public function it_can_fetch_with_query_parameter()
    {
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch', ['--q' => 'technology'])
            ->assertSuccessful();

        $this->assertGreaterThan(0, Article::count());
    }

    /** @test */
    public function it_handles_api_failures_gracefully(): void
    {
        Http::fake([
            'newsapi.org/*' => Http::response([], 500),
            'gnews.io/*' => Http::response([], 500),
            'thenewsapi.com/*' => Http::response([], 500),
        ]);

        $this->artisan('news:fetch')
            ->assertSuccessful();

        $logs = FetchLog::all();

        $this->assertCount(3, $logs);

        foreach ($logs as $log) {
            $this->assertEquals(0, $log->articles_fetched);
            $this->assertEquals(0, $log->articles_saved);
            $this->assertEquals('completed', $log->status);
        }

        $this->assertEquals(0, Article::count());
    }

    /** @test */
    public function it_logs_fetch_statistics(): void
    {
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch')
            ->assertSuccessful();

        $logs = FetchLog::all();

        $this->assertCount(3, $logs);

        foreach ($logs as $log) {
            $this->assertNotNull($log->started_at);
            $this->assertNotNull($log->completed_at);
            $this->assertEquals('completed', $log->status);
            $this->assertGreaterThanOrEqual(0, $log->articles_fetched);
        }
    }

    /** @test */
    public function it_records_correct_article_counts_in_logs(): void
    {
        $this->mockNewsApiResponses();

        $this->artisan('news:fetch')
            ->assertSuccessful();

        $newsapiLog = FetchLog::where('source_api', 'newsapi')->first();

        $this->assertNotNull($newsapiLog);
        $this->assertEquals(1, $newsapiLog->articles_fetched);
        $this->assertEquals(1, $newsapiLog->articles_saved);
        $this->assertEquals(0, $newsapiLog->duplicates);
    }

    private function mockNewsApiResponses(): void
    {
        Http::fake([
            'newsapi.org/*' => Http::response([
                'articles' => [
                    [
                        'source' => ['id' => 'techcrunch', 'name' => 'TechCrunch'],
                        'author' => 'John Doe',
                        'title' => 'AI Revolution in 2024',
                        'description' => 'Testing description',
                        'url' => 'https://example.com/article-1',
                        'urlToImage' => 'https://example.com/image.jpg',
                        'content' => 'Full content here',
                        'publishedAt' => now()->toISOString(),
                    ],
                ],
            ], 200),

            'gnews.io/*' => Http::response([
                'articles' => [
                    [
                        'source' => ['name' => 'BBC News'],
                        'title' => 'Business News Today',
                        'description' => 'Testing description',
                        'url' => 'https://example.com/article-2',
                        'image' => 'https://example.com/image2.jpg',
                        'content' => 'Full content here',
                        'publishedAt' => now()->toISOString(),
                    ],
                ],
            ], 200),

            'thenewsapi.com/*' => Http::response([
                'data' => [
                    [
                        'source' => 'Reuters',
                        'title' => 'Science Breakthrough',
                        'description' => 'Testing description',
                        'url' => 'https://example.com/article-3',
                        'image_url' => 'https://example.com/image3.jpg',
                        'snippet' => 'Content snippet',
                        'published_at' => now()->toISOString(),
                        'categories' => ['science'],
                        'locale' => 'en',
                    ],
                ],
            ], 200),
        ]);
    }
}
