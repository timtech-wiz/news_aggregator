<?php

namespace App\Providers;

use App\Contracts\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository
        $this->app->singleton(
            ArticleRepositoryInterface::class,
            ArticleRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
