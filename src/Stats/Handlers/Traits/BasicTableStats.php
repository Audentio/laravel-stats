<?php

namespace Audentio\LaravelStats\Stats\Handlers\Traits;

use Audentio\LaravelBase\Foundation\AbstractModel;
use Carbon\CarbonImmutable;

trait BasicTableStats
{
    public function calculateCount(CarbonImmutable $date, ?AbstractModel $content, array $extraData = []): float
    {
        $additionalContentTypeConditionals = $this->getAdditionalContentTypeConditionalsForTableStats($content, $extraData) ?? null;

        $query= \DB::table($this->getTableNameForBasicTableStats())
            ->where($this->getDateConditionalsForQuery($date))
            ->where($this->getConditionalsForBasicTableStats($extraData))
            ->where($this->getCommonConditionalsForQuery($extraData));

        if ($additionalContentTypeConditionals) {
            $query->where($additionalContentTypeConditionals);
        }

        return $query->count();
    }

    protected function getAdditionalContentTypeConditionalsForTableStats(?AbstractModel $content, array $extraData = []): ?array
    {
        return null;
    }

    protected function getConditionalsForBasicTableStats(array $extraData = []): array
    {
        return [];
    }

    abstract protected function getTableNameForBasicTableStats(): string;
}