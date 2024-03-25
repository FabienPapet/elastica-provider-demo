<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch;

use ApiPlatform\Metadata\FilterInterface;
use ApiPlatform\Metadata\Operation;

interface ElasticFilterInterface extends FilterInterface
{
    public function apply(QueryBuilder $queryBuilder, Operation $operation, array $uriVariables = [], array $context = []): void;
}
