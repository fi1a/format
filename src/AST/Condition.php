<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use Fi1a\Collection\Collection;

/**
 * Условия форматирования строки
 */
class Condition extends Collection
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
