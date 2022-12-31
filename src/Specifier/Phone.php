<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Форматирование телефона
 */
class Phone implements SpecifierInterface
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) !== 1) {
            throw new InvalidArgumentException('Нужно передать один модификатор');
        }
        $format = (string) $modifiers[0];
        if ($format === '') {
            throw new InvalidArgumentException('Формат телефона не может быть пустым');
        }
        $value = (string) $value;
        $value = preg_replace('/[^0-9]/', '', $value);

        $current = 0;
        $index = 0;
        $result = '';
        $equals = true;
        do {
            $symbol = mb_substr($format, $current, 1);
            $current++;
            $valueSymbol = $index < mb_strlen($value) ? mb_substr($value, $index, 1) : '';
            if (preg_match('/[0-9]/', $symbol) > 0 && $valueSymbol !== $symbol) {
                $equals = false;
            }
            if ($symbol === 'd' || ($valueSymbol === $symbol && $equals)) {
                $index++;
                $result .= $valueSymbol;

                continue;
            }
            $result .= $symbol;
        } while ($current < mb_strlen($format));

        return $result;
    }
}
