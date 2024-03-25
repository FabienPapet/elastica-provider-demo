<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch;

use Elastica\Query\BoolQuery;

class QueryBuilder
{
    protected bool $count = false;

    protected string $index;

    protected BoolQuery $query;

    private array $sorts = [];

    public function __construct(string $index)
    {
        $this->index = $index;
        $this->query = new BoolQuery();
    }

    public function getQuery(): BoolQuery
    {
        return $this->query;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function addSort(array $sort): void
    {
        $this->sorts[] = $sort;
    }

    public function getSorts(): array
    {
        return $this->sorts;
    }

    public function isCount(): bool
    {
        return $this->count;
    }

    public function count(bool $count = true): self
    {
        $this->count = $count;

        return $this;
    }
}
