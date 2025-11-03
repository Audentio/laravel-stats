<?php

namespace Audentio\LaravelStats\Models\Traits;

use Audentio\LaravelBase\Foundation\AbstractModel;
use Audentio\LaravelBase\Foundation\Traits\ContentTypeTrait;
use Audentio\LaravelStats\LaravelStats;
use Audentio\LaravelStats\Stats\Statistic;
use Audentio\LaravelStats\Stats\StatisticAggregation;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Illuminate\Database\Query\Builder;

trait DailyStatModelTrait
{
    use ContentTypeTrait;

    public function fillStatsExtraData(array $extraData = []): void
    {

    }

    protected function initializeDailyStatModelTrait()
    {
        $this->fillable = array_merge($this->fillable, [
            'content_type', 'content_id', 'kind', 'sub_kind', 'date', 'value'
        ]);

        $this->casts = array_merge($this->casts, [
            'date' => 'datetime',
        ]);
    }

    public static function getStatisticsBaseQuery(Carbon $startDate, Carbon $endDate): Builder
    {
        return \DB::table('daily_stats')
            ->select(['kind', 'sub_kind', 'value', 'date'])
            ->orderBy('date')
            ->where([
                ['date', '>=', $startDate],
                ['date', '<=', $endDate],
            ]);
    }

    /** @return StatisticAggregation[] */
    public static function getStatisticsData(Carbon $startDate, Carbon $endDate, string $aggregation,
                                             ?AbstractModel $content, array $keys): array
    {
        if (empty($keys)) {
            throw new \LogicException('Must set a list of stat keys to query');
        }

        $query = self::getStatisticsBaseQuery($startDate, $endDate);

        $statKeyParts = [];
        foreach (LaravelStats::getStatKeys() as $statKey) {
            $keyParts = explode('__', $statKey, 2);
            if (empty($keyParts[1])) {
                $keyParts[1] = null;
            }
            if (!array_key_exists($keyParts[0], $statKeyParts)) {
                $statKeyParts[$keyParts[0]] = [];
            }

            $statKeyParts[$keyParts[0]][$keyParts[1]] = false;
        }
        $query->where(function(Builder $query) use ($keys, &$statKeyParts) {
            foreach ($keys as $key) {
                $query->orWhere(function(Builder $query) use ($key, &$statKeyParts) {
                    $keyParts = explode('__', $key, 2);
                    $query->where('kind', $keyParts[0]);

                    if (isset($keyParts[1])) {
                        $query->where('sub_kind', $keyParts[1]);
                        $statKeyParts[$keyParts[0]][$keyParts[1]] = true;
                    } else {
                        foreach ($statKeyParts[$keyParts[0]] as &$keyPart) {
                            $keyPart = true;
                        }
                    }
                });
            }
        });

        $contentType = null;
        $contentId = null;
        if ($content) {
            $contentType = $content->getContentType();
            $contentId = $content->getKey();
        }

        $query->where([
            ['content_type', $contentType],
            ['content_id', $contentId],
        ]);

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
            case 'week':
                $intervalStart->startOfWeek(Carbon::SUNDAY);
                $periodInterval = CarbonInterval::createFromDateString('1 week');
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
            foreach ($statKeyParts as $kind => $subKinds) {
                foreach ($subKinds as $subKind => $include) {
                    $key = $kind . '__' . $subKind;
                    if (!$include) {
                        continue;
                    }
                    $statistics[$key] = new Statistic($kind, $subKind, $periodStart);
                }
            }
            foreach ($dataInRange as $item) {
                if (!array_key_exists($item->key, $statistics)) {
                    continue;
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