<?php

namespace Audentio\LaravelStats\Console\Commands;

use Audentio\LaravelStats\Jobs\BuildDailyStatsJob;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class RebuildStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audentio-stats:rebuild {--start=} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild daily stats data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queue = $this->option('queue');

        $start = $this->option('start');
        if ($start) {
            $start = new Carbon($start, new \DateTimeZone('UTC'));
        } else {
            $start = \DB::table('users')->selectRaw('min(created_at) AS start')->first();
            $start = $start->start ?? now()->subMonth();
            $start = new Carbon($start, new \DateTimeZone('UTC'));
        }
        $start->startOfDay();

        $end = now();
        $end->addDay()->startOfDay();
        $interval = CarbonInterval::createFromDateString('1 day');
        $period = new CarbonPeriod($start, $interval, $end);

        foreach ($period as $date) {
            $this->output->writeln($date->toAtomString());
            if ($queue) {
                BuildDailyStatsJob::dispatch($date);
            } else {
                BuildDailyStatsJob::dispatchSync($date);
            }
        }
        return 0;
    }
}
