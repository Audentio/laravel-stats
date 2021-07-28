<?php

namespace Audentio\LaravelStats\Stats;

use Carbon\Carbon;

class StatisticAggregation
{
    private Carbon $start;
    private Carbon $end;
    private string $aggregation;
    private array $statistics = [];

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    public function getAggregation(): string
    {
        return $this->aggregation;
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

    public function addStatistic(Statistic $statistic): void
    {
        $this->statistics[] = $statistic;
    }

    public function addStatistics(array $statistics): void
    {
        foreach ($statistics as $statistic) {
            $this->addStatistic($statistic);
        }
    }

    public function __construct(Carbon $start, Carbon $end, string $aggregation)
    {
        $this->start = $start;
        $this->end = $end;
        $this->aggregation = $aggregation;
    }
}