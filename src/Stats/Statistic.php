<?php

namespace Audentio\LaravelStats\Stats;

use Audentio\LaravelStats\LaravelStats;
use Audentio\LaravelStats\Stats\Handlers\AbstractStatHandler;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class Statistic
{

    private string $kind;
    private string $subKind;
    private Carbon $date;
    private string $overviewMethod;

    private ?float $value = null;
    private array $values = [];

    //['min', 'max', 'avg', 'sum'],

    public function getKey(): string
    {
        return $this->kind . '__' . $this->subKind;
    }

    public function getValue(): float
    {
        if ($this->value === null) {
            if (empty($this->values)) {
                $this->value = 0.0;
                return $this->value;
            }
            $this->value = match($this->overviewMethod) {
                'min' => min($this->values),
                'max' => max($this->values),
                'avg' => array_sum($this->values) / count($this->values),
                'sum' => array_sum($this->values),
                default => throw new \LogicException('Unknown overview method: ' . $this->overviewMethod),
            };
        }

        return $this->value;
    }

    public function getValueString(): string
    {
        return $this->getHandler()->formatValueString($this->subKind, $this->getValue());
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getHandler(): AbstractStatHandler
    {
        return LaravelStats::getHandlerInstanceForStatKey($this->getKey());
    }

    public function addValue(float $value): void
    {
        if ($this->value !== null) {
            $this->value = null;
        }
        $this->values[] = $value;
    }

    public function __construct(string $kind, string $subKind, Carbon|string $date)
    {
        if (!$date instanceof Carbon) {
            $date = new Carbon($date, new CarbonTimeZone('UTC'));
        }

        $this->kind = $kind;
        $this->subKind = $subKind;
        $this->date = $date;
        $this->overviewMethod = LaravelStats::getOverviewMethodForStatKey($kind . '__' . $subKind);
    }
}
