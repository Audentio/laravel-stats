<?php

namespace Audentio\LaravelStats\Models\Traits;

trait DailyStatModelTrait
{
    protected function initializeDailyStatModelTrait()
    {
        $this->fillable = array_merge($this->fillable, [
            'kind', 'sub_kind', 'date', 'value'
        ]);

        $this->casts = array_merge($this->casts, [
            'date' => 'datetime',
        ]);
    }
}