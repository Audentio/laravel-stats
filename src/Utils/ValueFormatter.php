<?php

namespace Audentio\LaravelStats\Utils;

use Google\Service\Sheets\NumberFormat;

class ValueFormatter
{
    const VALUE_FORMAT_FLOAT = 'float';
    const VALUE_FORMAT_NUMBER = 'number';
    const VALUE_FORMAT_CURRENCY = 'currency';
    const VALUE_FORMAT_FILE_SIZE = 'fileSize';

    public static function format(float $value, string $type, array $options = []): string
    {
        $method = 'format' . ucfirst($type);
        if (empty($type) || !method_exists(get_called_class(), $method)) {
            throw new \LogicException('Invalid type: ' . $type);
        }

        return static::$method($value, $options);
    }

    public static function formatFileSize(float $value, array $options = []): string
    {
        $units = [
            'tb' => 1000 * 1000 * 1000 * 1000,
            'gb' => 1000 * 1000 * 1000,
            'mb' => 1000 * 1000,
            'kb' => 1000,
            'b' => 1,
        ];

        $unit = null;
        foreach ($units as $unit => $multiplier) {
            if ($value < $multiplier) {
                continue;
            }

            $value = $value / $multiplier;
            break;
        }

        if ($unit === 'b') {
            return static::formatNumber($value) . $unit;
        } else {
            return static::formatFloat($value) . $unit;
        }
    }

    public static function formatFloat(float $value, array $options = []): string
    {
        $options = array_replace([
            'locale' => 'en-US',
            'attributes' => [
                \NumberFormatter::DECIMAL_ALWAYS_SHOWN => true,
                \NumberFormatter::MIN_FRACTION_DIGITS => 1,
            ],
        ], $options);

        return static::getNumberFormatter($options['locale'], \NumberFormatter::DECIMAL, $options['attributes'])
            ->format($value);
    }

    public static function formatNumber(float $value, array $options = []): string
    {
        $options = array_replace([
            'locale' => 'en-US',
            'attributes' => [],
        ], $options);

        $options['attributes'][\NumberFormatter::DECIMAL_ALWAYS_SHOWN] = false;
        $options['attributes'][\NumberFormatter::MIN_FRACTION_DIGITS] = 0;
        $options['attributes'][\NumberFormatter::MAX_FRACTION_DIGITS] = 0;
        $options['attributes'][\NumberFormatter::FRACTION_DIGITS] = 0;
        $options['attributes'][\NumberFormatter::ROUND_FLOOR] = true;

        return static::formatFloat($value, $options);
    }

    public static function formatCurrency(float $value, array $options = []): string
    {
        $options = array_replace([
            'locale' => 'en-US',
            'currency' => 'usd',
            'attributes' => [],
        ], $options);

        return static::getNumberFormatter($options['locale'], \NumberFormatter::CURRENCY, $options['attributes'])
            ->formatCurrency($value, $options['currency']);
    }

    public static function getNumberFormatter(string $locale, string $style, array $attributes = []): \NumberFormatter
    {
        $numberFormatter = new \NumberFormatter($locale, $style);

        foreach ($attributes as $key => $value) {
            if ($value === null) {
                continue;
            }
            $numberFormatter->setAttribute($key, $value);
        }

        return $numberFormatter;
    }
}