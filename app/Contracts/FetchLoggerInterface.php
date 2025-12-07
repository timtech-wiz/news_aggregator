<?php

namespace App\Contracts;

interface FetchLoggerInterface
{
    public function startFetch(string $source): int;

    public function completeFetch(int $logId, array $stats): void;

    public function failFetch(int $logId, string $error): void;
}
