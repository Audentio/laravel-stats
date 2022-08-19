<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Enums;

use Audentio\LaravelGraphQL\GraphQL\Support\Enum;

class StatisticTagEnum extends Enum
{
    protected $attributes = [
        'name' => 'StatisticTagEnum',
        'description' => 'An enum type',
        'values' => null,
    ];

    public function __construct()
    {
        $values = array_keys(config('audentioStats.statTags')) ?? [];

        if (empty($values)) {
            $values = ['_no_values'];
        }

        $this->attributes['values'] = $values;
    }
}