<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Types;

use Audentio\LaravelStats\GraphQL\Resources\StatisticResource;
use Audentio\LaravelGraphQL\GraphQL\Support\Type as GraphQLType;

class StatisticType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Statistic',
        'description' => 'A type'
    ];

    protected function getResourceClassName(): string
    {
        return StatisticResource::class;
    }
}