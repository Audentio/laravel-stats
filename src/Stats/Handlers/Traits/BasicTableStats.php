<?php

namespace Audentio\LaravelStats\Stats\Handlers\Traits;

use Carbon\Carbon;

trait BasicTableStats
{
    public function calculateCount(Carbon $date): float
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