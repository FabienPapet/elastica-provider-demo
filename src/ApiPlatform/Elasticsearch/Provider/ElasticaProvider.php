<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\ApiPlatform\Elasticsearch\Pagination\Paginator;
use App\ApiPlatform\Elasticsearch\QueryBuilder;
use App\ApiPlatform\Elasticsearch\StateOptions\StateOptions;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastica\Client;
use Elastica\Query;
use Elastica\Result;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ElasticaProvider implements ProviderInterface
{
    public function __construct(
        private readonly Pagination $pagination,
        private readonly ContainerInterface $filterLocator,
        private readonly Client $client,
        private readonly DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $stateOptions = $operation->getStateOptions();

        if (!$stateOptions instanceof StateOptions) {
            throw new \InvalidArgumentException();
        }

        $builder = new QueryBuilder($stateOptions->getIndexName());
        $this->applyFilters($operation, $uriVariables, $context, $builder);

        $index = $this->client->getIndex($builder->getIndex());

        $query = new Query();
        $query->setQuery($builder->getQuery());

        if ($builder->isCount()) {
            $count = $index->count($query);

            return (new Paginator([], 0, $count))->setTotalItems($count);
        }

        foreach ($builder->getSorts() as $sort) {
            $query->addSort($sort);
        }

        if ($stateOptions->trackTotalHits()) {
            $query->setTrackTotalHits();
        }

        if ($this->pagination->isEnabled($operation, $context)) {
            [, $offset, $limit] = $this->pagination->getPagination($operation, $context);

            $query->setSize($limit);
            $query->setFrom($offset);
        }

        $results = $index->search($query); // will do the query

        $objects = array_map(function(Result $result) use ($context) {
            return $this->denormalizer->denormalize($result->getData(), $context['resource_class']);
        }, $results->getResults());

        $paginator = new Paginator($objects, $offset, $limit);
        $paginator->setTotalItems($results->getTotalHits());

        return $paginator;
    }

    private function applyFilters(Operation $operation, array $uriVariables, array $context, QueryBuilder $queryBuilder): void
    {
        foreach ($operation->getFilters() as $filterServiceAlias) {
            if ($this->filterLocator->has($filterServiceAlias)) {
                $this->filterLocator->get($filterServiceAlias)->apply($queryBuilder, $operation, $uriVariables, $context);
            }
        }
    }
}
