<?php

namespace Audentio\LaravelStats\Models\Traits;

use Audentio\LaravelStats\Stats\Statistic;
use Audentio\LaravelStats\Stats\StatisticAggregation;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Illuminate\Database\Query\Builder;

trait DailyStatModelTrait
{
    protected function initializeDailyStatModelTrait()
    {
        $this->fillable = array_merge($this->fillable, [
            'kind', 'sub_kind', 'date', 'value'
        ]);

        $this->casts = array_merge($this->casts, [
            'date' => 'datetime',
        ]);
    }

    /** @return StatisticAggregation[] */
    public static function getStatisticsData(Carbon $startDate, Carbon $endDate, string $aggregation,
                                             ?array $limitKeys = null): array
    {
        $query = \DB::table('daily_stats')
            ->select(['kind', 'sub_kind', 'value', 'date'])
            ->orderBy('date')
            ->where([
                ['date', '>=', $startDate],
                ['date', '<=', $endDate],
            ]);

        if ($limitKeys) {
            $query->where(function(Builder $query) use ($limitKeys) {
                foreach ($limitKeys as $key) {
                    $query->orWhere(function(Builder $query) use ($key) {
                        $keyParts = explode('__', $key, 2);
                        $query->where('kind', $keyParts[0]);

                        if (isset($keyParts[1])) {
                            $query->where('sub_kind', $keyParts[1]);
                        }
                    });
                }
            });
        }

        $data = $query->get()->all();
        foreach ($data as $key=>$item) {
            $item->key = $item->kind . '__' . $item->sub_kind;
            $item->date = new Carbon($item->date, new CarbonTimeZone('UTC'));
        }
        $data = collect($data);

        $intervalStart = clone $startDate;
        $intervalEnd = clone $endDate;
        $intervalEnd->startOfDay();

        switch ($aggregation) {
            case 'day':
                $periodInterval = CarbonInterval::createFromDateString('1 day');
                break;
            case 'month':
                $intervalStart->startOfMonth();
                $periodInterval = CarbonInterval::createFromDateString('1 month');
                break;
            default:
                throw new \RuntimeException('Invalid aggregation: ' . $aggregation);
        }

        $period = new CarbonPeriod($intervalStart, $intervalEnd, $periodInterval);

        $statisticAggregations = [];

        /** @var Carbon $periodStart */
        foreach ($period as $periodStart) {
            $periodEnd = clone $periodStart;
            $periodEnd->add($periodInterval)
                ->subSecond();

            $statisticAggregation = new StatisticAggregation($periodStart, $periodEnd, $aggregation);

            $dataInRange = $data->filter(function ($data) use ($periodStart, $periodEnd) {
                if ($data->date > $periodEnd) {
                    return false;
                }

                if ($data->date < $periodStart) {
                    return false;
                }

                return true;
            })->all();

            $statistics = [];
            foreach ($dataInRange as $item) {
                if (!array_key_exists($item->key, $statistics)) {
                    $statistics[$item->key] = new Statistic($item->kind, $item->sub_kind, $periodStart);
                }

                /** @var Statistic $statistic */
                $statistic = $statistics[$item->key];

                $statistic->addValue($item->value);
            }

            $statisticAggregation->addStatistics($statistics);

            $statisticAggregations[] = $statisticAggregation;
        }

        return $statisticAggregations;
    }
}