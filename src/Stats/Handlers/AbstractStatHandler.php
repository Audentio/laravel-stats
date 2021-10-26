<?php

namespace Audentio\LaravelStats\Stats\Handlers;

use App\Models\DailyStat;
use Audentio\LaravelStats\Models\Interfaces\DailyStatModelInterface;
use Audentio\LaravelStats\Stats\DailyStatData;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

abstract class AbstractStatHandler
{
    public function buildStatsForDate(CarbonImmutable $date, array $extraData = []): void
    {
        foreach ($this->getSubKinds() as $subKind) {
            $data = $this->buildStatForDate($subKind, $date, $extraData);
            if ($data) {
                $this->storeDailyStatData($data, $extraData);
            }
        }
    }

    protected function getCommonConditionalsForQuery(array $extraData = [])
    {
        return [];
    }

    protected function getDateConditionalsForQuery(CarbonImmutable $date, string $columnName = 'created_at'): array
    {
        $start = $date->startOfDay();
        $end = $date->endOfDay();

        return [
            [$columnName, '>=', $start],
            [$columnName, '<=', $end],
        ];
    }

    protected function buildStatForDate(string $subKind, CarbonImmutable $date, array $extraData = []): ?DailyStatData
    {
        $methodName = 'calculate' . ucfirst($subKind);
        if (!method_exists($this, $methodName)) {
            throw new \RuntimeException('Invalid sub kind: ' . $subKind . ' (Expected method: ' . $methodName . '())');
        }

        $value = $this->$methodName($date, $extraData);

        return new DailyStatData($this->getKind(), $subKind, $date, $value, $extraData);
    }

    protected function storeDailyStatData(DailyStatData $data, array $extraData = []): void
    {
        $className = config('audentioStats.statsModel');

        /** @var DailyStatModelInterface $dailyStat */
        $dailyStat = $className::firstOrNew($data->getDataToFindExistingModel());
        $dailyStat->fillStatsExtraData($extraData);
        $dailyStat->value = $data->getValue();
        $dailyStat->save();
    }

    abstract public function getKind(): string;
    abstract public function getSubKinds(): array;
}