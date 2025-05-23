<?php

declare(strict_types=1);

namespace Audentio\LaravelStats\GraphQL\Resources;

use Audentio\LaravelGraphQL\GraphQL\Definitions\Type;
use Audentio\LaravelGraphQL\GraphQL\Support\Resource as GraphQLResource;
use Audentio\LaravelGraphQL\Rebing\GraphQL\GraphQL;
use Audentio\LaravelStats\LaravelStats;

class StatisticKeyResource extends GraphQLResource
{
    public function getExpectedModelClass(): ?string
    {
        return null;
    }

    public function getOutputFields(string $scope): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'resolve' => function ($root) {
                    return $root;
                }
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function ($root) {
                    return LaravelStats::getStatKeyName($root);
                }
            ],
            'overview_method' => [
                'type' => Type::nonNull(\GraphQL::type('StatisticOverviewMethodEnum')),
                'resolve' => function ($root) {
                    return LaravelStats::getOverviewMethodForStatKey($root);
                }
            ],
            'supported_content_types' => [
                'type' => Type::listOf(\GraphQL::type('StatisticContentTypeEnum')),
                'resolve' => function ($root) {
                    return LaravelStats::getSupportedContentTypesForStatKey($root);
                }
            ],
            'tags' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(\GraphQL::type('StatisticTagEnum')))),
                'resolve' => function ($root) {
                    return LaravelStats::getTagsForStatKey($root);
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
        return 'StatisticKey';
    }
}