<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Metadata\Operation;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use App\ApiPlatform\Elasticsearch\QueryHelper;

class RangeFilter extends AbstractFilter
{
    private const FILTER_RANGE_DELIMITER = ';';

    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        foreach ($this->properties as $requestParameter => $searchField) {
            $value = $this->getRequestValue($requestParameter);

            if (!is_string($value) || !str_contains($value, self::FILTER_RANGE_DELIMITER)) {
                continue;
            }

            $parts = explode(self::FILTER_RANGE_DELIMITER, $value);

            if (count($parts) !== 2) {
                continue;
            }

            $queryBuilder->getQuery()->addMust(
                QueryHelper::createRange($searchField, (float) $parts[0], (float) $parts[1]),
            );
        }
    }

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
                    'example' => '0;100',
                    'type' => 'string',
                    'explode' => false,
                ],
            ];
        }

        return $documentation;
    }
}
