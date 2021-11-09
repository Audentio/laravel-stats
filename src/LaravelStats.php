<?php

namespace Audentio\LaravelStats;

class LaravelStats
{
    protected static bool $runsMigrations = true;
    protected static bool $usesUniqueKeyOnDailyStats = true;
    protected static bool $addsGraphQLSchema = true;
    protected static bool $addsCliCommands = true;
    
    public static function runsMigrations(): bool
    {
        return self::$runsMigrations;
    }

    public static function skipMigrations(): void
    {
        self::$runsMigrations = false;
    }

    public static function usesUniqueKeyOnDailyStats(): bool
    {
        return self::$usesUniqueKeyOnDailyStats;
    }

    public static function skipUniqueKeyOnDailyStats(): void
    {
        self::$usesUniqueKeyOnDailyStats = false;
    }

    public static function addsCliCommands(): bool
    {
        return self::$addsCliCommands;
    }

    public static function skipCliCommands(): void
    {
        self::$addsCliCommands = false;
    }

    public static function addsGraphQLSchema(): bool
    {
        return self::$addsGraphQLSchema;
    }

    public static function skipGraphQLSchema(): void
    {
        self::$addsGraphQLSchema = false;
    }

    public static function getStatHandlers(): array
    {
        return config('audentioStats.statHandlers') ?? [];
    }

    public static function getStatKeys(): array
    {
        return array_keys(config('audentioStats.statKeys')) ?? [];
    }

    public static function getSupportedContentTypesForStatKey(string $key): array
    {
        return config('audentioStats.statKeys.' . $key)['content_types'] ?? [];
    }

    public static function getStatKeyName(string $key): string
    {
        $parts = explode('__', $key, 2);
        $phraseKey = 'statistics.keyNames.' . $parts[0] . (count($parts) > 1 ? '.' . $parts[1] : '');

        return __($phraseKey);
    }
}