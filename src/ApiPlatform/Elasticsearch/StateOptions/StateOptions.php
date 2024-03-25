<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\StateOptions;

use ApiPlatform\State\OptionsInterface;

class StateOptions implements OptionsInterface
{
    public function __construct(
        private readonly string $indexName,
        private readonly bool $trackTotalHits = false,
    ) {
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function trackTotalHits(): bool
    {
        return $this->trackTotalHits;
    }
}
