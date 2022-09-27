<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Интерфейс AST
 */
interface IAST
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
    public function getNodes(): INodes;
}
