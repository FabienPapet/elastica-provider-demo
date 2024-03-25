<?php

namespace App\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GetCollection;
use App\ApiPlatform\Elasticsearch\Provider\ElasticaProvider;
use App\ApiPlatform\Elasticsearch\StateOptions\StateOptions;
use Symfony\Component\Serializer\Attribute\Groups;


#[GetCollection(
    normalizationContext: [
        'groups' => ['customer:read'],
    ],
    provider: ElasticaProvider::class,
    stateOptions: new StateOptions(
        indexName: 'customers',
    ),
)]
class Customer
{
    #[ApiProperty(identifier: true)]
    public int $id;

    #[Groups(['customer:read'])]
    public string $firstname;
    public string $lastname;
}
