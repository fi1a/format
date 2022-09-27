<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Модификатор спецификатора
 */
interface IModifier extends INode
{
    /**
     * Конструктор
     *
     * @param mixed $value
     * @param mixed[] $values
     */
    public function __construct($value, array $values, bool $isVariable);

    /**
     * Возвращает значение
     *
     * @return mixed
     */
    public function getValue();
}
