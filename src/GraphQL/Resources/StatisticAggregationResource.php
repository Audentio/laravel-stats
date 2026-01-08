<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Resources;

use Audentio\LaravelGraphQL\GraphQL\Definitions\Type;
use Audentio\LaravelGraphQL\GraphQL\Support\Resource as GraphQLResource;
use Audentio\LaravelStats\Stats\StatisticAggregation;
use GraphQL;

class StatisticAggregationResource extends GraphQLResource
{
    public function getExpectedModelClass(): ?string
    {
        return null;
    }

    public function getOutputFields(string $scope): array
    {
        return [
            'start' => [
                'type' => Type::nonNull(Type::timestamp()),
                'resolve' => function (StatisticAggregation $aggregation) {
                    return $aggregation->getStart();
                }
            ],

            'aggregation' => [
                'type' => Type::nonNull(GraphQL::type('StatisticAggregationEnum')),
                'resolve' => function (StatisticAggregation $aggregation) {
                    return $aggregation->getAggregation();
                }
            ],

            'statistics' => [
                'type' => Type::listOf(GraphQL::type('Statistic')),
                'resolve' => function (StatisticAggregation $aggregation) {
                    return $aggregation->getStatistics();
                }
            ],

            'updated_at' => [
                'type' => Type::timestamp(),
                'resolve' => function (StatisticAggregation $aggregation) {
                    return $aggregation->getUpdatedAt();
                }
            ],
        ];
    }

    public function getInputFields(string $scope, bool $update = false): array
    {
        return [];
    }

    public function getCommonFields(string $scope, bool $update = false): array
    {
        return [];
    }

    public function getGraphQLTypeName(): string
    {
        return 'StatisticAggregation';
    }
}
