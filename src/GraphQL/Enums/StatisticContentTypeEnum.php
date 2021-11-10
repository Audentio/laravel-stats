<?php

namespace Audentio\LaravelStats\GraphQL\Enums;

use App\Models\AchievementRuleCondition;
use Audentio\LaravelGraphQL\GraphQL\Definitions\Enums\ContentType\ContentTypeEnum;

class StatisticContentTypeEnum extends ContentTypeEnum
{
    protected function _getContentTypes(): array
    {
        $values = config('audentioStats.contentTypes') ?? ['no_values'];

        $return = [];
        foreach ($values as $value) {
            if ($value === null) continue;

            $return[] = $value;
        }
        return $return;
    }
}
