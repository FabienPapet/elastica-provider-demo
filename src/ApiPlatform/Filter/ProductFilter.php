<?php

namespace App\ApiPlatform\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\Filter\AbstractFilter;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use App\ApiPlatform\Elasticsearch\QueryHelper;
use Elastica\Query\BoolQuery;

class ProductFilter extends AbstractFilter
{
    private const FILTER_NAME = 'query';

    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$this->filterExists(self::FILTER_NAME)) {
            return;
        }

        $searchQuery = $this->getRequestValue(self::FILTER_NAME);

        if ('' === $searchQuery) {
            return;
        }

        $query = new BoolQuery();

        $query->addShould(QueryHelper::matchExactly($searchQuery, 'code', 10));
        $query->addShould(QueryHelper::matchByRegex($searchQuery, 'code', 2));
        $query->addShould(QueryHelper::matchPartiallyOnMultipleFields($searchQuery, ['name^3', 'brand^2', 'model^1']));
        $query->addShould(QueryHelper::matchPartiallyWithFaultTolerance($searchQuery, ['name', 'brand', 'model']));

        $queryBuilder->getQuery()->addMust($query);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::FILTER_NAME => [
                'property' => 'query',
                'type' => 'string',
                'required' => false,
                'is_collection' => false,
                'openapi' => [
                    'example' => 'Gilet Jaune',
                ],
            ]
        ];
    }
}
