<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\{
    NewsApiClientInterface,
    ArticleRepositoryInterface,
    NewsAggregatorInterface,
    FetchLoggerInterface
};
use App\Services\{
    NewsAggregatorService,
    FetchLoggerService
};
use App\Services\NewsApi\{
    NewsApiClient,
    GNewsClient,
    TheNewsApiClient
};
use App\Repositories\ArticleRepository;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repository
        $this->app->singleton(
            ArticleRepositoryInterface::class,
            ArticleRepository::class
        );

        // Bind Logger
        $this->app->singleton(
            FetchLoggerInterface::class,
            FetchLoggerService::class
        );

        // Bind News Clients
        $this->app->singleton('news.clients', function () {
            return [
                $this->app->make(NewsApiClient::class),
                $this->app->make(GNewsClient::class),
                $this->app->make(TheNewsApiClient::class),
            ];
        });

        // Bind Aggregator with dependency injection
        $this->app->singleton(
            NewsAggregatorInterface::class,
            function ($app) {
                return new NewsAggregatorService(
                    $app->make(ArticleRepositoryInterface::class),
                    $app->make(FetchLoggerInterface::class),
                    $app->make('news.clients')
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}
