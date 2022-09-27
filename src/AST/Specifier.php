<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Спецификатор
 */
class Specifier implements ISpecifier
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var IModifier[]
     */
    private $modifiers = [];

    /**
     * @inheritDoc
     */
    public function __construct(string $name, array $modifiers)
    {
        $this->name = $name;
        $this->modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }
}
