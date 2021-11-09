<?php

namespace Audentio\LaravelStats\Stats;

use Audentio\LaravelBase\Foundation\AbstractModel;
use Carbon\CarbonImmutable;

class DailyStatData
{
    protected string $kind;
    protected string $subKind;
    protected CarbonImmutable $date;
    protected ?AbstractModel $content;
    protected float $value;

    public function getContent(): ?AbstractModel
    {
        return $this->content;
    }

    public function getContentType(): ?string
    {
        return $this->content?->getContentType();
    }

    public function getContentId(): ?string
    {
        return $this->content?->getKey();
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDataToFindExistingModel(): array
    {
        $data = [
            'content_type' => $this->getContentType(),
            'content_id' => $this->getContentId(),
            'kind' => $this->kind,
            'sub_kind' => $this->subKind,
            'date' => $this->date->format('Y-m-d'),
        ];

        return $data;
    }

    public function __construct(string $kind, string $subKind, CarbonImmutable $date, ?AbstractModel $content, float $value)
    {
        $this->kind = $kind;
        $this->subKind = $subKind;
        $this->date = $date;
        $this->content = $content;
        $this->value = $value;
    }
}