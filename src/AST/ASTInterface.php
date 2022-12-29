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
     * @param mixed[] $values
     * @param mixed[] $modifierValues
     */
    public function __construct(string $string, array $values = [], array $modifierValues = []);

    /**
     * Возвращает список узлов
     */
    public function getNodes(): NodesInterface;
}
