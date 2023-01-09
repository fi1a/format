<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use InvalidArgumentException;

/**
 * Склонение слов после числительных
 */
class Declension implements SpecifierInterface
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) !== 3) {
            throw new InvalidArgumentException('Необходимо передать три значения аргумента для склонения слова');
        }
        /**
         * @var string $one
         * @var string $four
         * @var string $five
         */
        [$one, $four, $five] = $modifiers;

        $number = (int) $value;

        $number = $number % 100;
        if ($number > 19) {
            $number = $number % 10;
        }

        if ($number === 1) {
            return $one;
        } elseif ($number > 1 && $number < 5) {
            return $four;
        }

        return $five;
    }
}
