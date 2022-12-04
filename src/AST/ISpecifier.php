<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Спецификатор
 */
interface ISpecifier extends NodeInterface
{
    /**
     * Конструктор
     *
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(string $name, array $modifiers);

    /**
     * Возвращает название
     */
    public function getName(): string;

    /**
     * Возвращает модификаторы
     *
     * @return ModifierInterface[]
     */
    public function getModifiers(): array;
}
