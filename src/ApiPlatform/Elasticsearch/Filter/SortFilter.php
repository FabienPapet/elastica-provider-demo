<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;

class SortFilter extends AbstractFilter
{
    private const ORDER_ASC = 'ASC';

    private const ORDER_DESC = 'DESC';

    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$this->filterExists('order')) {
            return;
        }

        $query = $this->extractArrayValues('order');

        foreach ($query as $searchField => $direction) {
            $direction = $this->getDirection((string) $query[$searchField]);
            if ($direction === null) {
                continue;
            }

            if (!array_key_exists($searchField, $this->properties)) {
                continue;
            }

            $this->addSort($queryBuilder, $searchField, $direction);
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }

    protected function getDirection(string $value): ?string
    {
        $direction = strtoupper($value);

        if ($direction !== self::ORDER_ASC && $direction !== self::ORDER_DESC) {
            return null;
        }

        return $direction;
    }

    protected function addSort(QueryBuilder $queryBuilder, string $searchField, string $direction): void
    {
        $queryBuilder->addSort([$this->properties[$searchField] => $direction]);
    }
}
