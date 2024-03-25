<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use App\ApiPlatform\Elasticsearch\QueryHelper;

class ExistsFilter extends AbstractFilter
{
    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        foreach ($this->properties as $requestParameter => $searchField) {
            if (!$this->filterExists($requestParameter)) {
                continue;
            }

            $queryBuilder->getQuery()->addMust(QueryHelper::fieldIsNotNull($searchField));
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $documentation = [];
        foreach (array_keys($this->properties) as $requestParameter) {
            $documentation[$requestParameter] = [
                'property' => $requestParameter,
                'type' => 'bool',
                'required' => false,
                'is_collection' => false,
                'openapi' => [
                    'type' => 'boolean',
                    'explode' => false,
                ],
            ];
        }

        return $documentation;
    }
}
