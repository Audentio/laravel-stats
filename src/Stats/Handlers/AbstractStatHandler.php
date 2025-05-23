<?php

namespace Audentio\LaravelStats\Stats\Handlers;

use App\Models\DailyStat;
use Audentio\LaravelBase\Foundation\AbstractModel;
use Audentio\LaravelStats\Models\Interfaces\DailyStatModelInterface;
use Audentio\LaravelStats\Stats\DailyStatData;
use Audentio\LaravelStats\Utils\ValueFormatter;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

abstract class AbstractStatHandler
{
    public function getOverviewMethod(?string $subKind = null): string
    {
        return 'sum';
    }

    public function getValueType(string $subKind): string
    {
        return ValueFormatter::VALUE_FORMAT_NUMBER;
    }

    public function getValueFormatOptions(string $subKind): array
    {
        return [];
    }

    public function getStatTags(): array
    {
        return [];
    }

    public function getSupportedContentTypes(): array
    {
        return [null];
    }

    public function formatValueString(string $subKind, float $value): string
    {
        return ValueFormatter::format($value, $this->getValueType($subKind), $this->getValueFormatOptions($subKind));
    }

    public function canQuery(): bool
    {
        return true;
    }

    public function buildStatsForDate(CarbonImmutable $date, array $extraData = []): void
    {
        foreach ($this->getSubKinds() as $subKind) {
            foreach ($this->getSupportedContentTypes() as $contentType) {
                foreach ($this->getSupportedContentModels($contentType, $extraData) as $content) {
                    $data = $this->buildStatForDate($subKind, $date, $content, $extraData);

                    if ($data) {
                        $this->storeDailyStatData($data, $content, $extraData);
                    }
                }
            }
        }
    }

    protected function getSupportedContentModels(?string $contentType, array $extraData): Collection
    {
        if ($contentType === null) {
            return collect([null]);
        }

        return $this->getSupportedContentModelsQuery($contentType, $extraData)->get();
    }

    protected function getSupportedContentModelsQuery(string $contentType, array $extraData): Builder
    {
        return $contentType::query();
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

    protected function getExtraConditionalsForFindingExistingModelOnStore(array $extraData): array
    {
        return [];
    }

    protected function buildStatForDate(string $subKind, CarbonImmutable $date, ?AbstractModel $content, array $extraData = []): ?DailyStatData
    {
        $methodName = 'calculate' . ucfirst($subKind);
        if (!is_callable([$this, $methodName])) {
            throw new \RuntimeException('Invalid sub kind: ' . $subKind . ' (Expected method: ' . $methodName . '())');
        }

        $value = $this->$methodName($date, $content, $extraData);

        return new DailyStatData($this->getKind(), $subKind, $date, $content, $value, $extraData);
    }

    protected function storeDailyStatData(DailyStatData $data, ?AbstractModel $content, array $extraData = []): void
    {
        $className = config('audentioStats.statsModel');

        /** @var DailyStatModelInterface|Model $dailyStat */
        $dailyStat = $className::where($this->getExtraConditionalsForFindingExistingModelOnStore($extraData))->firstOrNew($data->getDataToFindExistingModel());

        if ($data->getValue() === 0.0) {
            if ($dailyStat->exists()) {
                $dailyStat->delete();
            }

            return;
        }
        $dailyStat->fillStatsExtraData($extraData);
        if ($content !== null) {
            $dailyStat->content_type = $content->getContentType();
            $dailyStat->content_id = $content->getKey();
        }
        $dailyStat->value = $data->getValue();
        $dailyStat->save();
    }

    abstract public function getKind(): string;
    abstract public function getSubKinds(): array;
}