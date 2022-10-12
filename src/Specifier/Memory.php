<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Форматирование размера памяти
 */
class Memory implements ISpecifier
{
    /**
     * @var string[]
     */
    protected static $dimensions = [
        'B' => 'Б',
        'KB' => 'КБ',
        'MB' => 'МБ',
        'GB' => 'ГБ',
        'TB' => 'ТБ',
        'PB' => 'ПБ',
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
         * @var string $dimension
         */
        $dimension = isset($modifiers[0]) ? (string) $modifiers[0] : null;

        if ($dimension === 'PB' || (!$dimension && abs($value) >= 1024 * 1024 * 1024 * 1024 * 1024)) {
            return sprintf('%.1f', $value / 1024 / 1024 / 1024 / 1024 / 1024) . ' ' . static::$dimensions['PB'];
        }
        if ($dimension === 'TB' || (!$dimension && abs($value) >= 1024 * 1024 * 1024 * 1024)) {
            return sprintf('%.1f', $value / 1024 / 1024 / 1024 / 1024) . ' ' . static::$dimensions['TB'];
        }
        if ($dimension === 'GB' || (!$dimension && abs($value) >= 1024 * 1024 * 1024)) {
            return sprintf('%.1f', $value / 1024 / 1024 / 1024) . ' ' . static::$dimensions['GB'];
        }
        if ($dimension === 'MB' || (!$dimension && abs($value) >= 1024 * 1024)) {
            return sprintf('%.1f', $value / 1024 / 1024) . ' ' . static::$dimensions['MB'];
        }
        if ($dimension === 'KB' || (!$dimension && abs($value) >= 1024)) {
            return sprintf('%.1f', $value / 1024) . ' ' . static::$dimensions['KB'];
        }

        return sprintf('%.1f', $value) . ' ' . static::$dimensions['B'];
    }

    /**
     * Установить единицы измерения
     *
     * @param string[] $dimensions
     */
    public static function setDimensions(array $dimensions): bool
    {
        foreach (['B', 'KB', 'MB', 'GB', 'TB', 'PB'] as $dimension) {
            if (!array_key_exists($dimension, $dimensions)) {
                throw new InvalidArgumentException(sprintf('"%s" key not provided', $dimension));
            }
        }
        static::$dimensions = $dimensions;

        return true;
    }
}
