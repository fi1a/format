<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

/**
 * Интерфейс спецификатора
 */
interface ISpecifier
{
    /**
     * Форматирование значения с переданными модификаторами спецификатора
     *
     * @param mixed $value
     * @param mixed ...$modifiers
     */
    public function format($value, ...$modifiers): string;
}
