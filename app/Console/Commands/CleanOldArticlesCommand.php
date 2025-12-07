<?php

namespace App\Console\Commands;

use App\Contracts\ArticleRepositoryInterface;
use Illuminate\Console\Command;

class CleanOldArticlesCommand extends Command
{
    protected $signature = 'news:clean {--days=30 : Days to keep articles}';

    protected $description = 'Remove articles older than specified days';

    public function handle(ArticleRepositoryInterface $repository): int
    {
        $days = (int) $this->option('days');

        $this->info("Cleaning articles older than {$days} days...");

        $deleted = $repository->deleteOlderThan($days);

        $this->info("✓ Deleted {$deleted} articles");

        return Command::SUCCESS;
    }
}
