<?php

namespace App\ApiPlatform\Output;

use ApiPlatform\Metadata\ApiProperty;

class SearchResult
{
    #[ApiProperty(identifier: true)]
    public string $code;
    public string $brand;
    public string $model;
    public string $name;
    public \DateTimeImmutable $lastUpdated;
}
