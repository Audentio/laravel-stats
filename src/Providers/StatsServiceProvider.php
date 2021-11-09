<?php

namespace Audentio\LaravelStats\Providers;

use Audentio\LaravelGraphQL\LaravelGraphQL;
use Audentio\LaravelStats\Console\Commands\RebuildStatsCommand;
use Audentio\LaravelStats\GraphQL\Enums\StatisticAggregationEnum;
use Audentio\LaravelStats\GraphQL\Enums\StatisticContentTypeEnum;
use Audentio\LaravelStats\GraphQL\Queries\Statistic\StatisticKeysQuery;
use Audentio\LaravelStats\GraphQL\Queries\Statistic\StatisticsQuery;
use Audentio\LaravelStats\GraphQL\Types\StatisticAggregationType;
use Audentio\LaravelStats\GraphQL\Types\StatisticKeyType;
use Audentio\LaravelStats\GraphQL\Types\StatisticType;
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
            $this->registerGraphQLSchema();
        }
    }

    protected function registerGraphQLSchema(): void
    {
        if (LaravelStats::addsGraphQLSchema()) {
            $schema = [
                'types' => [
                    'StatisticAggregation' => StatisticAggregationType::class,
                    'StatisticKey' => StatisticKeyType::class,
                    'Statistic' => StatisticType::class,
                    'StatisticAggregationEnum' => StatisticAggregationEnum::class,
                    'StatisticContentTypeEnum' => StatisticContentTypeEnum::class,
                ],
                'queries' => [
                    'statisticKeys' => StatisticKeysQuery::class,
                    'statistics' => StatisticsQuery::class,
                ],
            ];

            $overrides = config('audentioStats.graphQLSchemaOverrides');
            foreach ($schema as $schemaType => &$values) {
                foreach ($values as $key => &$value) {
                    if (isset($overrides[$schemaType][$value])) {
                        $value = $overrides[$schemaType][$value];
                    } else if (isset($overrides[$schemaType][$key])) {
                        $value = $overrides[$schemaType][$key];
                    }
                }
            }

            LaravelGraphQL::registerTypes($schema['types']);
            LaravelGraphQL::registerQueries($schema['queries']);
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
        if (LaravelStats::addsCliCommands()) {
            $this->commands([
                RebuildStatsCommand::class,
            ]);
        }
    }
}