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
     */
    public function __construct(string $string, array $values = []);

    /**
     * Возвращает список узлов
     */
    public function getNodes(): NodesInterface;
}
