<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiPlatform\Elasticsearch\Filter\CountFilter;
use App\ApiPlatform\Elasticsearch\Filter\RangeFilter;
use App\ApiPlatform\Elasticsearch\Filter\TermFilter;
use App\ApiPlatform\Elasticsearch\Provider\ElasticaProvider;
use App\ApiPlatform\Elasticsearch\StateOptions\StateOptions;
use App\ApiPlatform\Filter\ProductFilter;
use App\Entity\Traits\EntityIdTrait;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping\Column;

use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/products/search',
            provider: ElasticaProvider::class,
            stateOptions: new StateOptions(
                indexName: 'products',
                trackTotalHits: true,
            )
        ),
        new GetCollection(),
        new Get(),
    ],
)]
#[ApiFilter(filterClass: ProductFilter::class)]
#[ApiFilter(filterClass: TermFilter::class, properties: ['brand' => 'brand'])]
#[ApiFilter(filterClass: RangeFilter::class, properties: ['price' => 'min_price'])]
#[ApiFilter(filterClass: CountFilter::class, properties: ['count'])]
class Product
{
    use EntityIdTrait;

    #[Column]
    #[ApiProperty(identifier: true)]
    private string $code;

    #[Column]
    private string $brand;

    #[Column]
    private string $model;

    #[Column]
    private string $name;

    #[SerializedName('min_price')]
    private float $minPrice;
    private string $categories;

    #[Column(length: 3)]
    private string $currency;

    #[Column]
    private \DateTimeImmutable $lastUpdated;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLastUpdated(): \DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTimeImmutable $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getMinPrice(): string
    {
        return $this->minPrice;
    }

    public function setMinPrice(float $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): void
    {
        $this->categories = $categories;
    }
}
