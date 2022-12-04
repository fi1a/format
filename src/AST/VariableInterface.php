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
     * @param mixed[]         $values
     */
    public function __construct(string $path, array $values = [], ?SpecifierInterface $specifier = null);

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
    public function getSpecifier(): ?SpecifierInterface;
}