<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Filter;

use App\ApiPlatform\Elasticsearch\ElasticFilterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractFilter implements ElasticFilterInterface
{
    public function __construct(
        protected RequestStack $requestStack,
        protected array $properties = [],
    ) {
    }

    protected function extractArrayValues(string $requestParameterName): array
    {
        $requestParameters = $this->requestStack->getMainRequest()->query->all();

        if (!array_key_exists($requestParameterName, $requestParameters)) {
            return [];
        }

        return $requestParameters[$requestParameterName];
    }

    protected function getRequestValue(string $requestParameterName): ?string
    {
        $requestParameters = $this->requestStack->getMainRequest()->query->all();

        return $requestParameters[$requestParameterName] ?? null;
    }

    protected function filterExists(string $requestParameterName): bool
    {
        $requestParameters = $this->requestStack->getMainRequest()->query->all();

        return array_key_exists($requestParameterName, $requestParameters);
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
