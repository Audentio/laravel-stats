<?php

namespace Audentio\LaravelStats\Stats;

use Carbon\Carbon;

class DailyStatData
{
    protected string $kind;
    protected string $subKind;
    protected Carbon $date;
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
            'date' => $this->date,
        ];
    }

    public function __construct(string $kind, string $subKind, Carbon $date, float $value)
    {
        $this->kind = $kind;
        $this->subKind = $subKind;
        $this->date = $date;
        $this->value = $value;
    }
}