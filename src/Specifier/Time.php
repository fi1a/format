<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Форматирование времени
 */
class Time implements ISpecifier
{
    /**
     * @var string[][]|int[][]
     */
    private static $timeFormats = [
        [0, 'less_sec'],
        [1, 'sec'],
        [59, 'secs', 1],
        [60, 'min'],
        [3599, 'mins', 60],
        [5400, 'hr'],
        [86399, 'hrs', 3600],
        [129600, 'day'],
        [604800, 'days', 86400],
    ];

    /**
     * @var string[]
     */
    private static $timeLabels = [
        'less_sec' => '< 1 сек.',
        'sec' => '1 сек.',
        'secs' => 'сек.',
        'min' => '1 мин.',
        'mins' => 'мин.',
        'hr' => '1 ч.',
        'hrs' => 'ч.',
        'day' => '1 д.',
        'days' => 'д.',
    ];

    /**
     * @var int[]
     */
    private static $time = [
        'seconds' => 2,
        'minutes' => 4,
        'hours' => 6,
        'days' => 8,
    ];

    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 1) {
            throw new InvalidArgumentException('More than one modifier passed');
        }

        $value = (int) $value;

        /**
         * @var string $time
         */
        $time = isset($modifiers[0]) ? mb_strtolower((string) $modifiers[0]) : null;
        if ($time && !array_key_exists($time, static::$time)) {
            throw new InvalidArgumentException(
                sprintf('Неизвестный аргумент спецификатора "%s"', $time)
            );
        }

        if ($time) {
            $format = static::$timeFormats[static::$time[$time]];
            assert(is_string($format[1]));
            assert(is_int($format[2]));

            return floor($value / $format[2]) . ' ' . static::$timeLabels[$format[1]];
        }

        foreach (static::$timeFormats as $format) {
            if ($value > $format[0]) {
                continue;
            }
            assert(is_string($format[1]));
            if (count($format) === 2) {
                return static::$timeLabels[$format[1]];
            }
            assert(is_int($format[2]));

            return ceil($value / $format[2]) . ' ' . static::$timeLabels[$format[1]];
        }
        $format = end(static::$timeFormats);
        assert(is_string($format[1]));
        assert(is_int($format[2]));

        return ceil($value / $format[2]) . ' ' . static::$timeLabels[$format[1]];
    }

    /**
     * Установить единицы измерения времени
     *
     * @param string[] $times
     */
    public static function setTimes(array $times): bool
    {
        foreach (['less_sec', 'sec', 'secs', 'min', 'mins', 'hr', 'hrs', 'day', 'days',] as $time) {
            if (!array_key_exists($time, $times)) {
                throw new InvalidArgumentException(sprintf('"%s" key not provided', $time));
            }
        }
        static::$timeLabels = $times;

        return true;
    }
}
