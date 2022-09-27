<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Счетчик
 */
class Counter
{
    /**
     * @var int
     */
    private $counter = 0;

    /**
     * Увеличивает значение счетчика
     */
    public function increment(): void
    {
        $this->counter++;
    }

    /**
     * Уменьшает значение счетчика
     */
    public function decrement(): void
    {
        $this->counter--;
    }

    /**
     * Возвращает значение счетчика
     */
    public function get(): int
    {
        return $this->counter;
    }
}
