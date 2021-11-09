<?php

namespace Audentio\LaravelStats\GraphQL\Enums;

use App\Models\AchievementRuleCondition;
use Audentio\LaravelGraphQL\GraphQL\Definitions\Enums\ContentType\ContentTypeEnum;

class StatisticContentTypeEnum extends ContentTypeEnum
{
    protected function _getContentTypes(): array
    {
        return config('audentioStats.contentTypes') ?? [null];
    }
}
