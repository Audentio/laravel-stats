<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Queries\Statistic;

use App\Core;
use Audentio\LaravelBase\Utils\ContentTypeUtil;
use Audentio\LaravelGraphQL\GraphQL\Definitions\Type;
use Audentio\LaravelGraphQL\GraphQL\Support\Query;
use Audentio\LaravelGraphQL\GraphQL\Traits\FilterableQueryTrait;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class StatisticsQuery extends Query
{
    use FilterableQueryTrait;

    protected static StatisticsQuery $instance;

    protected $attributes = [
        'name' => 'StatisticsQuery',
        'description' => 'Retrieve a list of statistics.'
    ];

    public static function getQueryType(): GraphQLType
    {
        return Type::listOf(GraphQL::type('StatisticAggregation'));
    }

    public static function getFilters(): array
    {
        return [
            'key_ids' => [
                'type' => Type::listOf(Type::nonNull(Type::id())),
                'hasOperator' => false,
            ],
        ];
    }

    public static function getQueryArgs($scope = ''): array
    {
        $args = [
            'aggregation' => [
                'type' => GraphQL::type('StatisticAggregationEnum'),
                'description' => 'Defaults to \'day\' if none set.',
            ],
            'content_type' => [
                'type' => GraphQL::type('StatisticContentTypeEnum'),
                'description' => 'Defaults to NULL if none set.',
                'rules' => ['required_with:content_id'],
            ],
            'content_id' => [
                'type' => Type::id(),
                'rules' => ['required_with:content_type'],
            ],
            'start_date' => [
                'type' => Type::timestamp(),
                'description' => 'If not specified the past 30 days will be shown.',
                'rules' => [
                    'required_with:end_date',
                    function($attribute, $value, $fail) {
                        if ($value > now()) {
                            $fail(__('statistics.errors.cannotSelectStartDateInFuture'));
                        }
                    }
                ],
            ],
            'end_date' => ['type' => Type::timestamp()],
        ];
        self::addFilterArgs($scope, $args);

        return $args;
    }

    public static function getResolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        $daysLimit = 730; // 2 year limit
        $instance = self::$instance;

        $contentType = null;
        $contentId = null;
        $content = null;
        if (isset($args['content_type'])) {
            $contentType = ContentTypeUtil::getModelClassNameForContentType($args['content_type']);
            $contentId = $args['content_id'];

            $content = ContentTypeUtil::getModelByContentTypeAndId($contentType, $contentId);
            if (!$content) {
                $instance->notFoundError($info);
            }
        }

        if (!Core::viewer()->canViewStatistics($content)) {
            $instance->permissionError($info);
        }

        $aggregation = $args['aggregation'] ?? 'day';
        $startDate = $args['start_date'] ?? null;
        if (!$startDate) {
            $startDate = now();
            $startDate->subDays(30);
        }
        $startDate->startOfDay();

        $endDate = $args['end_date'] ?? null;
        if (!$endDate) {
            $endDate = now();
        }
        if ($endDate < $startDate) {
            $endDate = clone $startDate;
        }
        $endDate->endOfDay();

        $diff = $startDate->diff($endDate);
        if ($diff->days > $daysLimit) {
            $instance->invalidParameterError($info, __('statistics.errors.cannotQueryMorThanXDays', ['days' => $daysLimit]));
        }

        $limitKeys = $args['filter']['key_ids'] ?? null;

        $className = config('audentioStats.statsModel');

        return $className::getStatisticsData(
            $startDate,
            $endDate,
            $aggregation,
            $content,
            $limitKeys
        );
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