<?php

namespace App\Console\Commands;

use App\Contracts\NewsAggregatorInterface;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    protected $signature = 'news:fetch
                          {--q= : Search query}
                          {--category= : Article category}';

    protected $description = 'Fetch articles from configured news sources';

    public function handle(NewsAggregatorInterface $aggregator): int
    {
        $this->info('Starting news aggregation...');

        $params = array_filter([
            'q' => $this->option('q'),
            'category' => $this->option('category'),
        ]);

        $results = $aggregator->aggregate($params);

        $this->displayResults($results);

        return Command::SUCCESS;
    }

    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('✓ Aggregation Complete!');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Fetched', $results['total_fetched']],
                ['Total Saved', $results['total_saved']],
                ['Duplicates', $results['total_duplicates']],
            ]
        );

        if (!empty($results['sources'])) {
            $this->newLine();
            $this->info('Source Breakdown:');

            foreach ($results['sources'] as $source => $data) {
                $this->line("  • {$source}: {$data['saved']} saved, {$data['duplicates']} duplicates");
            }
        }
    }
}
