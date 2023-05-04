<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Resources;

use Audentio\LaravelGraphQL\GraphQL\Definitions\Type;
use Audentio\LaravelGraphQL\GraphQL\Support\Resource as GraphQLResource;
use Audentio\LaravelStats\Stats\Statistic;
use GraphQL;

class StatisticResource extends GraphQLResource
{
    public function getExpectedModelClass(): ?string
    {
        return null;
    }

    public function getOutputFields(string $scope): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(GraphQL::type('StatisticKey')),
                'resolve' => function (Statistic $statistic) {
                    return $statistic->getKey();
                }
            ],
            'value' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => function (Statistic $statistic) {
                    return $statistic->getValue();
                }
            ],
            'valueString' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (Statistic $statistic) {
                    return $statistic->getValueString();
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
        return 'Statistic';
    }
}