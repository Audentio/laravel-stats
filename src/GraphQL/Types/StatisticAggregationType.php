<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Types;

use Audentio\LaravelStats\GraphQL\Resources\StatisticAggregationResource;
use Audentio\LaravelGraphQL\GraphQL\Support\Type as GraphQLType;

class StatisticAggregationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'StatisticAggregation',
        'description' => 'A type'
    ];

    protected function getResourceClassName(): string
    {
        return StatisticAggregationResource::class;
    }
}