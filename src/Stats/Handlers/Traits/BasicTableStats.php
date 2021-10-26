<?php

namespace Audentio\LaravelStats\Stats\Handlers\Traits;

use Carbon\CarbonImmutable;

trait BasicTableStats
{
    public function calculateCount(CarbonImmutable $date, array $extraData = []): float
    {
        return (float) \DB::table($this->getTableNameForBasicTableStats())
            ->where($this->getDateConditionalsForQuery($date))
            ->where($this->getConditionalsForBasicTableStats($extraData))
            ->where($this->getCommonConditionalsForQuery($extraData))
            ->count();
    }

    protected function getConditionalsForBasicTableStats(array $extraData = []): array
    {
        return [];
    }

    abstract protected function getTableNameForBasicTableStats(): string;
}