<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;

class ArrayFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        $documentation = [];
        foreach (array_keys($this->properties) as $requestParameter) {
            $documentation[$requestParameter] = [
                'property' => $requestParameter,
                'type' => 'array',
                'required' => false,
                'is_collection' => true,
                'openapi' => [
                    'explode' => false,
                ],
            ];
        }

        return $documentation;
    }

    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        foreach ($this->properties as $requestParameter => $searchField) {
            $values = $this->extractArrayValues($requestParameter);

            if ($values === []) {
                continue;
            }

            $queryBuilder->getQuery()->addMust(
                $this->createQuery($searchField, $values),
            );
        }
    }

    private function createQuery(string $elasticSearchField, array $values): BoolQuery
    {
        $filterBoolQuery = new BoolQuery();

        foreach ($values as $value) {
            $matchQuery = new MatchQuery($elasticSearchField, $value);
            $filterBoolQuery->addShould($matchQuery);
        }

        return $filterBoolQuery;
    }
}
