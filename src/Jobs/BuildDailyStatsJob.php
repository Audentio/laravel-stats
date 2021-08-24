<?php

namespace Audentio\LaravelStats\Jobs;

use Audentio\LaravelStats\Stats\Handlers\AbstractStatHandler;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuildDailyStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $dateString;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = (new CarbonImmutable($this->dateString))->startOfDay();
        $handlers = config('audentioStats.statHandlers');
        foreach ($handlers as $handlerClass) {
            /** @var AbstractStatHandler $instance */
            $instance = new $handlerClass;
            $instance->buildStatsForDate($date);
        }
    }

    public function __construct(CarbonInterface $date)
    {
        $this->dateString = $date->toString();

    }
}
