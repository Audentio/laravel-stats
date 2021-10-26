<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Types;

use Audentio\LaravelStats\GraphQL\Resources\StatisticKeyResource;
use Audentio\LaravelGraphQL\GraphQL\Support\Type as GraphQLType;

class StatisticKeyType extends GraphQLType
{
    protected $attributes = [
        'name' => 'StatisticKey',
        'description' => 'A type'
    ];

    protected function getResourceClassName(): string
    {
        return StatisticKeyResource::class;
    }
}