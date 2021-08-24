<?php

namespace Audentio\LaravelStats\Stats\Handlers\Traits;

use Carbon\CarbonImmutable;

trait BasicTableStats
{
    public function calculateCount(CarbonImmutable $date): float
    {
        return (float) \DB::table($this->getTableNameForBasicTableStats())
            ->where($this->getDateConditionalsForQuery($date))
            ->where($this->getConditionalsForBasicTableStats())
            ->count();
    }

    protected function getConditionalsForBasicTableStats(): array
    {
        return [];
    }

    abstract protected function getTableNameForBasicTableStats(): string;
}