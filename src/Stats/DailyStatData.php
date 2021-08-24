<?php

namespace Audentio\LaravelStats\Stats;

use Carbon\CarbonImmutable;

class DailyStatData
{
    protected string $kind;
    protected string $subKind;
    protected CarbonImmutable $date;
    protected float $value;

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDataToFindExistingModel(): array
    {
        return [
            'kind' => $this->kind,
            'sub_kind' => $this->subKind,
            'date' => $this->date->format('Y-m-d'),
        ];
    }

    public function __construct(string $kind, string $subKind, CarbonImmutable $date, float $value)
    {
        $this->kind = $kind;
        $this->subKind = $subKind;
        $this->date = $date;
        $this->value = $value;
    }
}