<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Fixtures;

/**
 * Спецификатор для тестов
 */
class Specifier implements \Fi1a\Format\Specifier\ISpecifier
{
    /**
     * @inheritDoc
     */
    public function format(string $value, ...$modifiers): string
    {
        $modifier = (string) reset($modifiers);
        if (!$modifier) {
            $modifier = 's';
        }
        $modifier = '%1$' . $modifier;

        return sprintf($modifier, $value);
    }
}
