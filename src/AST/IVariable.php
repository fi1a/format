<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Переменная
 */
interface IVariable extends NodeInterface
{
    /**
     * Конструктор
     *
     * @param mixed[]         $values
     */
    public function __construct(string $path, array $values = [], ?ISpecifier $specifier = null);

    /**
     * Возвращает значение
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Возвращает ключ
     */
    public function getKey(): string;

    /**
     * Возвращает спецификатор
     */
    public function getSpecifier(): ?ISpecifier;
}
