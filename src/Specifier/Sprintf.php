<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

/**
 * Спецификатор функции sprintf
 */
class Sprintf implements ISpecifier
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 1) {
            throw new \InvalidArgumentException('More than one modifier passed');
        }

        /**
         * @var string $modifier
         */
        $modifier = reset($modifiers);
        if (!$modifier) {
            $modifier = 's';
        }
        /** @psalm-suppress PossiblyInvalidOperand */
        $modifier = '%1$' . $modifier;

        return sprintf($modifier, (string) $value);
    }
}
