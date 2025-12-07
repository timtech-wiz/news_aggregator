<?php

namespace App\Services;

use App\Contracts\FetchLoggerInterface;
use App\Models\FetchLog;

class FetchLoggerService implements FetchLoggerInterface
{
    public function startFetch(string $source): int
    {
        $log = FetchLog::create([
            'source_api' => $source,
            'status' => 'running',
            'started_at' => now(),
        ]);

        return $log->id;
    }

    public function completeFetch(int $logId, array $stats): void
    {
        FetchLog::where('id', $logId)->update([
            'articles_fetched' => $stats['fetched'] ?? 0,
            'articles_saved' => $stats['saved'] ?? 0,
            'duplicates' => $stats['duplicates'] ?? 0,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function failFetch(int $logId, string $error): void
    {
        FetchLog::where('id', $logId)->update([
            'status' => 'failed',
            'error_message' => $error,
            'completed_at' => now(),
        ]);
    }
}
