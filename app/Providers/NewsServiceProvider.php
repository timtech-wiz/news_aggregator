<?php

namespace App\Providers;

use App\Contracts\ArticleRepositoryInterface;
use App\Contracts\FetchLoggerInterface;
use App\Contracts\NewsAggregatorInterface;
use App\Services\FetchLoggerService;
use App\Services\NewsAggregatorService;
use App\Services\NewsApi\GNewsClient;
use App\Services\NewsApi\NewsApiClient;
use App\Services\NewsApi\TheNewsApiClient;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {

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
