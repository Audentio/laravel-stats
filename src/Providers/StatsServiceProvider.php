<?php

namespace Audentio\LaravelStats\Providers;

use Audentio\LaravelStats\Console\Commands\RebuildStatsCommand;
use Audentio\LaravelStats\LaravelStats;
use Illuminate\Support\ServiceProvider;

class StatsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/audentioStats.php', 'audentioStats'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
            $this->registerPublishes();
            $this->registerCommands();
        }
    }

    protected function registerMigrations(): void
    {
        if (LaravelStats::runsMigrations()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }

    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/audentioStats.php' => config_path('audentioStats.php'),
        ]);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            RebuildStatsCommand::class,
        ]);
    }
}