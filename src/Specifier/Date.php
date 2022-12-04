<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Спецификатор функции date
 */
class Date implements SpecifierInterface
{
    /**
     * @var string
     */
    protected static $defaultFormat = 'd.m.Y H:i:s';

    /**
     * @var string
     */
    protected static $defaultLocale = 'ru';

    /**
     * @var string[][]
     */
    protected static $dayOfWeek3 = [];

    /**
     * @var string[][]
     */
    protected static $dayOfWeek = [];

    /**
     * @var string[][]
     */
    protected static $fullMonthName = [];

    /**
     * @var string[][]
     */
    protected static $fullMonthNameAccusative = [];

    /**
     * @var string[][]
     */
    protected static $monthName3 = [];

    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 2) {
            throw new InvalidArgumentException('More than two modifier passed');
        }

        /**
         * @var string $format
         */
        $format = $modifiers[0] ?? null;
        $locale = isset($modifiers[1]) ? mb_strtolower((string) $modifiers[1]) : null;
        if (!$locale) {
            $locale = static::$defaultLocale;
        }
        if (!$format) {
            $format = static::$defaultFormat;
        }
        if (!is_numeric($value)) {
            $value = strtotime((string) $value);
        }
        $value = (int) $value;

        $index = 0;
        $result = '';
        while ($symbol = mb_substr($format, $index, 1)) {
            $dateSymbol = $symbol;
            if ($symbol === 'f') {
                $dateSymbol = 'F';
            }
            $current = date($dateSymbol, $value);
            switch ($symbol) {
                case 'D':
                    if (isset(static::$dayOfWeek3[$locale][$current])) {
                        $current = static::$dayOfWeek3[$locale][$current];
                    }

                    break;
                case 'l':
                    if (isset(static::$dayOfWeek[$locale][$current])) {
                        $current = static::$dayOfWeek[$locale][$current];
                    }

                    break;
                case 'F':
                    if (isset(static::$fullMonthName[$locale][$current])) {
                        $current = static::$fullMonthName[$locale][$current];
                    }

                    break;
                case 'f':
                    if (isset(static::$fullMonthNameAccusative[$locale][$current])) {
                        $current = static::$fullMonthNameAccusative[$locale][$current];
                    }

                    break;
                case 'M':
                    if (isset(static::$monthName3[$locale][$current])) {
                        $current = static::$monthName3[$locale][$current];
                    }

                    break;
            }
            $result .= $current;
            $index++;
        }

        return $result;
    }

    /**
     * Задать формат по умолчанию
     */
    public static function setDefaultFormat(string $defaultFormat): bool
    {
        if (!$defaultFormat) {
            throw new InvalidArgumentException('$defaultFormat can\'t be empty');
        }
        static::$defaultFormat = $defaultFormat;

        return true;
    }

    /**
     * Задать формат по умолчанию
     */
    public static function setDefaultLocale(string $defaultLocale): bool
    {
        if (!$defaultLocale) {
            throw new InvalidArgumentException('$defaultLocale can\'t be empty');
        }
        static::$defaultLocale = $defaultLocale;

        return true;
    }

    /**
     * Текстовое представление дня недели, 3 символа
     *
     * @param string[]  $dayOfWeek3
     */
    public static function setDayOfWeek3(string $locale, array $dayOfWeek3): bool
    {
        if (!$locale) {
            throw new InvalidArgumentException('$locale can\'t be empty');
        }
        static::$dayOfWeek3[mb_strtolower($locale)] = $dayOfWeek3;

        return true;
    }

    /**
     * Текстовое представление дня недели
     *
     * @param string[]  $dayOfWeek
     */
    public static function setDayOfWeek(string $locale, array $dayOfWeek): bool
    {
        if (!$locale) {
            throw new InvalidArgumentException('$locale can\'t be empty');
        }
        static::$dayOfWeek[mb_strtolower($locale)] = $dayOfWeek;

        return true;
    }

    /**
     * Полное наименование месяца
     *
     * @param string[]  $fullMonthName
     */
    public static function setFullMonthName(string $locale, array $fullMonthName): bool
    {
        if (!$locale) {
            throw new InvalidArgumentException('$locale can\'t be empty');
        }
        static::$fullMonthName[mb_strtolower($locale)] = $fullMonthName;

        return true;
    }

    /**
     * Полное наименование месяца
     *
     * @param string[]  $fullMonthNameAccusative
     */
    public static function setFullMonthNameAccusative(string $locale, array $fullMonthNameAccusative): bool
    {
        if (!$locale) {
            throw new InvalidArgumentException('$locale can\'t be empty');
        }
        static::$fullMonthNameAccusative[mb_strtolower($locale)] = $fullMonthNameAccusative;

        return true;
    }

    /**
     * Полное наименование месяца
     *
     * @param string[]  $monthName3
     */
    public static function setMonthName3(string $locale, array $monthName3): bool
    {
        if (!$locale) {
            throw new InvalidArgumentException('$locale can\'t be empty');
        }
        static::$monthName3[mb_strtolower($locale)] = $monthName3;

        return true;
    }
}
