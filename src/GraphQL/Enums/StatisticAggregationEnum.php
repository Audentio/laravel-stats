<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Enums;

use Audentio\LaravelGraphQL\GraphQL\Support\Enum;

class StatisticAggregationEnum extends Enum
{
    protected $enumObject = true;

    protected $attributes = [
        'name' => 'StatisticAggregationEnum',
        'description' => 'An enum type',
        'values' => ['day', 'week', 'month'],
    ];
}