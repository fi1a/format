<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Переменная
 */
interface VariableInterface extends NodeInterface
{
    /**
     * Конструктор
     *
     * @param mixed[] $values
     * @param SpecifierInterface[] $specifiers
     */
    public function __construct(string $path, array $values = [], array $specifiers = []);

    /**
     * Возвращает значение
     */
    public function getValue(): string;

    /**
     * Возвращает ключ
     */
    public function getKey(): string;

    /**
     * Возвращает спецификатор
     *
     * @return SpecifierInterface[]
     */
    public function getSpecifiers(): array;
}
