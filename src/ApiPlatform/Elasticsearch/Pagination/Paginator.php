<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Pagination;

use ApiPlatform\State\Pagination\PaginatorInterface;
use Elastica\Document;
use Elastica\ResultSet;

final class Paginator implements \IteratorAggregate, PaginatorInterface
{
    private \Traversable $iterator;

    private int $totalItems;

    public function __construct(
        ResultSet|array $datas,
        private readonly int $firstResult,
        private readonly int $maxResults,
    ) {
        if ($maxResults > 0) {
            $this->iterator = new \LimitIterator(new \ArrayIterator($this->getResults($datas)), 0, $maxResults);
        } else {
            $this->iterator = new \EmptyIterator();
        }

        $this->totalItems = $datas instanceof ResultSet ? $datas->getTotalHits() : count($datas);
    }

    public function getCurrentPage(): float
    {
        if ($this->maxResults <= 0) {
            return 1.;
        }

        return floor($this->firstResult / $this->maxResults) + 1.;
    }

    public function getLastPage(): float
    {
        if ($this->maxResults <= 0) {
            return 1.;
        }

        return ceil($this->totalItems / $this->maxResults);
    }

    public function getItemsPerPage(): float
    {
        return (float) $this->maxResults;
    }

    public function getTotalItems(): float
    {
        return (float) $this->totalItems;
    }

    public function count(): int
    {
        return iterator_count($this->iterator);
    }

    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }

    private function getResults(ResultSet|array $datas): array
    {
        if (!$datas instanceof ResultSet) {
            return $datas;
        }

        $results = [];

        foreach ($datas->getResults() as $result) {
            /** @var Document $result */
            $results[] = $result->getData();
        }

        return $results;
    }

    public function setTotalItems(int $totalItems): self
    {
        $this->totalItems = $totalItems;

        return $this;
    }
}
