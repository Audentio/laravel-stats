<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Enums;

use Audentio\LaravelGraphQL\GraphQL\Support\Enum;

class StatisticOverviewMethodEnum extends Enum
{
    protected $enumObject = true;

    protected $attributes = [
        'name' => 'StatisticOverviewMethodEnum',
        'description' => 'An enum type',
        'values' => ['min', 'max', 'avg', 'sum'],
    ];
}