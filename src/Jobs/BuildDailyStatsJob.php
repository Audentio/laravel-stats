<?php

namespace Audentio\LaravelStats\Jobs;

use Audentio\LaravelStats\Stats\Handlers\AbstractStatHandler;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuildDailyStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Carbon $date;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $handlers = config('audentioStats.statHandlers');
        foreach ($handlers as $handlerClass) {
            /** @var AbstractStatHandler $instance */
            $instance = new $handlerClass;
            $instance->buildStatsForDate($this->date);
        }
    }

    public function __construct(Carbon $date)
    {
        $date->startOfDay();
        $this->date = $date;
    }
}
