<?php

declare(strict_types=1);

namespace App\ApiPlatform\Elasticsearch;

use Elastica\Query\Exists;
use Elastica\Query\MultiMatch;
use Elastica\Query\Range;
use Elastica\Query\Regexp;
use Elastica\Query\Term;

class QueryHelper
{
    public static function matchExactly(string $search, string $field, int $boost = 0): Term
    {
        return (new Term())->setTerm($field, $search, $boost);
    }

    public static function matchPartiallyOnMultipleFields(string $search, array $fields): MultiMatch
    {
        return (new MultiMatch())
            ->setQuery($search)
            ->setFields($fields)
        ;
    }

    public static function matchPartiallyWithFaultTolerance(string $search, array $fields): MultiMatch
    {
        return self::matchPartiallyOnMultipleFields($search, $fields)
            ->setFuzziness('AUTO')
        ;
    }

    public static function fieldIsNotNull(string $field): Exists
    {
        return new Exists($field);
    }

    public static function matchByRegex(string $search, string $field, int $boost = 1): Regexp
    {
        $search = sprintf('%s.*', $search);

        return new Regexp($field, $search, $boost);
    }

    public static function createRange(string $searchField, float $from, float $to): Range
    {
        return new Range($searchField, ['from' => $from, 'to' => $to]);
    }
}
