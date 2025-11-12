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
