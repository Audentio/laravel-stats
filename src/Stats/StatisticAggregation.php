<?php

namespace Audentio\LaravelStats\Stats;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class StatisticAggregation
{
    private Carbon $start;
    private Carbon $end;
    private string $aggregation;
    private array $statistics = [];
    private ?CarbonInterface $updatedAt = null;

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

    public function getUpdatedAt(): ?CarbonInterface
    {
        return $this->updatedAt;
    }

    public function addStatistic(Statistic $statistic): void
    {
        $this->statistics[] = $statistic;
        if ($statistic->getUpdatedAt()) {
            if ($this->updatedAt === null || $statistic->getUpdatedAt() > $this->updatedAt) {
                $this->updatedAt = $statistic->getUpdatedAt();
            }
        }
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
