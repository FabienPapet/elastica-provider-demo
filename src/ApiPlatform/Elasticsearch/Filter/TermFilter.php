<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use Elastica\Query\Term;

class TermFilter extends AbstractFilter
{
    public function getDescription(string $resourceClass): array
    {
        $documentation = [];
        foreach (array_keys($this->properties) as $requestParameter) {
            $documentation[$requestParameter] = [
                'property' => $requestParameter,
                'type' => 'string',
                'required' => false,
                'is_collection' => false,
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
            $value = $this->getRequestValue($requestParameter);

            if($value === '' || $value === null) {
                continue;
            }

            $queryBuilder->getQuery()->addMust(
                new Term([$searchField => $value]),
            );
        }
    }
}
