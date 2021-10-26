<?php

namespace Audentio\LaravelStats\Models\Interfaces;

interface DailyStatModelInterface
{
    public function fillStatsExtraData(array $extraData = []): void;
}