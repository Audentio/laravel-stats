<?php

namespace Audentio\LaravelStats\Stats\Handlers;

use App\Models\DailyStat;
use Audentio\LaravelStats\Stats\DailyStatData;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

abstract class AbstractStatHandler
{
    public function buildStatsForDate(CarbonImmutable $date): void
    {
        foreach ($this->getSubKinds() as $subKind) {
            $data = $this->buildStatForDate($subKind, $date);
            if ($data) {
                $this->storeDailyStatData($data);
            }
        }
    }

    protected function getDateConditionalsForQuery(CarbonImmutable $date, string $columnName = 'created_at'): array
    {
        $start = clone $date;
        $start->startOfDay();

        $end = clone $date;
        $end->endOfDay();

        return [
            [$columnName, '>=', $start],
            [$columnName, '<=', $end],
        ];
    }

    protected function buildStatForDate(string $subKind, CarbonImmutable $date): ?DailyStatData
    {
        $methodName = 'calculate' . ucfirst($subKind);
        if (!method_exists($this, $methodName)) {
            throw new \RuntimeException('Invalid sub kind: ' . $subKind . ' (Expected method: ' . $methodName . '())');
        }

        $value = $this->$methodName($date);

        return new DailyStatData($this->getKind(), $subKind, $date, $value);
    }

    protected function storeDailyStatData(DailyStatData $data): void
    {
        $dailyStat = DailyStat::firstOrNew($data->getDataToFindExistingModel());
        $dailyStat->value = $data->getValue();
        $dailyStat->save();
    }

    abstract public function getKind(): string;
    abstract public function getSubKinds(): array;
}