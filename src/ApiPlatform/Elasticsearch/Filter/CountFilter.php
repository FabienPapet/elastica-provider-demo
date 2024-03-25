<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;

class CountFilter extends AbstractFilter
{
    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$this->filterExists('count')) {
            return;
        }

        $value = filter_var($this->getRequestValue('count'), FILTER_VALIDATE_BOOLEAN);

        if (!$value) {
            return;
        }

        $queryBuilder->count();
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'count' => [
                'property' => 'count',
                'type' => 'bool',
                'required' => false,
                'is_collection' => false,
                'openapi' => [
                    'explode' => false,
                ],
            ]
        ];
    }
}
