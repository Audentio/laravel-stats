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

    private float $value = 0.0;

    public function getKey(): string
    {
        return $this->kind . '__' . $this->subKind;
    }

    public function getValue(): float
    {
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
        $this->value += $value;
    }

    public function __construct(string $kind, string $subKind, Carbon|string $date)
    {
        if (!$date instanceof Carbon) {
            $date = new Carbon($date, new CarbonTimeZone('UTC'));
        }

        $this->kind = $kind;
        $this->subKind = $subKind;
        $this->date = $date;
    }
}