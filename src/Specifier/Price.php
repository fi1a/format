<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Форматирование цены
 */
class Price extends Number
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 7) {
            throw new InvalidArgumentException('Передано больше чем 7 модификатора');
        }

        $value = (float) $value;

        $decimals = isset($modifiers[0]) ? (int) $modifiers[0] : 2;
        $decimalSeparator = isset($modifiers[1]) && $modifiers[1] ? (string) $modifiers[1] : '.';
        $thousandsSeparator = isset($modifiers[2]) && $modifiers[2] ? (string) $modifiers[2] : '';
        $allowZeroDecimal = isset($modifiers[3]) && $modifiers[3] === true;
        $round = isset($modifiers[4]) ? (int) $modifiers[4] : null;
        $roundPrecision = isset($modifiers[5]) ? (int) $modifiers[5] : 0;
        $floor = isset($modifiers[6]) && $modifiers[6] === true;

        if ($round > 0) {
            $value = round($value, $roundPrecision, $round);
        }
        if ($floor) {
            $value = floor($value);
        }

        return parent::format($value, $decimals, $decimalSeparator, $thousandsSeparator, $allowZeroDecimal);
    }
}
