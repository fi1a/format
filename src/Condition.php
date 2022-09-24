<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Collection\DataType\MapArrayObject;

/**
 * Условия форматирования строки
 */
class Condition extends MapArrayObject
{
    /**
     * Удовлетворяет условию или нет
     */
    public function isSatisfies(): bool
    {
        return !$this->count()
            || $this->reduce(function ($carry, $value) {
                return $carry && $value;
            }, true);
    }
}
