<?php

namespace Audentio\LaravelStats;

class LaravelStats
{
    protected static bool $runsMigrations = true;

    public static function skipMigrations(): bool
    {
        self::$runsMigrations = false;
    }

    public static function runsMigrations(): bool
    {
        return self::$runsMigrations;
    }
}