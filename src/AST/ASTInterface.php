<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Интерфейс AST
 */
interface ASTInterface
{
    /**
     * Конструктор
     *
     * @param mixed[]  $values
     * @param mixed[]  $modifierValues
     * @param string[] $alwaysSpecifiers
     */
    public function __construct(string $string, array $values = [], array $modifierValues = [], bool $escape = true);

    /**
     * Возвращает список узлов
     */
    public function getNodes(): NodesInterface;
}
