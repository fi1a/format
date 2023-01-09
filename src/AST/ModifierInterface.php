<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Модификатор спецификатора
 */
interface ModifierInterface extends NodeInterface
{
    /**
     * Конструктор
     *
     * @param mixed   $value
     * @param mixed[] $modifierValues
     */
    public function __construct($value, array $modifierValues, bool $isVariable);

    /**
     * Возвращает значение
     *
     * @return mixed
     */
    public function getValue();
}
