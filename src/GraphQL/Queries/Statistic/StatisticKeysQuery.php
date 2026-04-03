<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Queries\Statistic;

use App\Core;
use Audentio\LaravelGraphQL\GraphQL\Definitions\Type;
use Audentio\LaravelGraphQL\GraphQL\Support\Query;
use Audentio\LaravelStats\LaravelStats;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class StatisticKeysQuery extends Query
{
    protected static StatisticKeysQuery $instance;

    protected $attributes = [
        'name' => 'StatisticKeysQuery',
        'description' => 'Retrieve a list of statistic keys.'
    ];

    public static function getQueryType(): GraphQLType
    {
        return Type::listOf(GraphQL::type('StatisticKey'));
    }

    public static function getQueryArgs($scope = ''): array
    {
        return [];
    }

    public static function getResolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        if (!Core::viewer()->canViewStatistics(keys: true)) {
            self::$instance->permissionError($info);
        }

        $statKeys = LaravelStats::getStatKeysForGraphQL();

        foreach ($statKeys as $key=>$statKey) {
            $handler = LaravelStats::getHandlerInstanceForStatKey($statKey);
            if (!$handler->canQuery()) {
                unset($statKeys[$key]);
            }
        }

        $statKeys = array_values($statKeys);

        usort($statKeys, function (string $a, string $b) {
            $handlerA = LaravelStats::getHandlerInstanceForStatKey($a);
            $handlerB = LaravelStats::getHandlerInstanceForStatKey($b);
            $subKindA = explode('__', $a, 2)[1] ?? $a;
            $subKindB = explode('__', $b, 2)[1] ?? $b;
            $orderA = $handlerA->getDisplayOrder($subKindA);
            $orderB = $handlerB->getDisplayOrder($subKindB);

            if ($orderA === null && $orderB === null) return 0;
            if ($orderA === null) return 1;
            if ($orderB === null) return -1;
            return $orderA <=> $orderB;
        });

        return $statKeys;
    }

    public function resolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        return self::getResolve($root, $args, $context, $info, $getSelectFields);
    }

    public function __construct()
    {
        self::$instance = $this;
    }
}
