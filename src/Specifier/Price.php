<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Форматирование цены
 */
class Price implements SpecifierInterface
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 4) {
            throw new InvalidArgumentException('Передано больше чем 4 модификатора');
        }

        $value = (float) $value;

        $decimals = isset($modifiers[0]) && $modifiers[0] ? (int) $modifiers[0] : 2;
        $decimalSeparator = isset($modifiers[1]) && $modifiers[1] ? (string) $modifiers[1] : '.';
        $thousandsSeparator = isset($modifiers[2]) && $modifiers[2] ? (string) $modifiers[2] : '';
        $allowZeroDecimal = isset($modifiers[3]) && $modifiers[3] === true;

        if (!$allowZeroDecimal && $decimals) {
            $abs = abs($value - floor($value));
            $comma = number_format($abs, $decimals, '.', '');
            for ($index = 1; $index <= $decimals; $index++) {
                if (mb_substr($comma, ($index * -1), 1) !== '0') {
                    break;
                }
            }

            return number_format(
                $value,
                ($decimals - $index + 1),
                $decimalSeparator,
                $thousandsSeparator
            );
        }

        return number_format(
            $value,
            $decimals,
            $decimalSeparator,
            $thousandsSeparator
        );
    }
}
