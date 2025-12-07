<?php

namespace App\Contracts;

interface NewsAggregatorInterface
{
    public function aggregate(array $params = []): array;
}
