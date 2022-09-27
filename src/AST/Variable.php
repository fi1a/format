<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Переменная
 */
class Variable implements IVariable
{
    use TValue;

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed[]
     */
    private $values;

    /**
     * @var ISpecifier|null
     */
    private $specifier;

    /**
     * @inheritDoc
     */
    public function __construct(string $key, array $values = [], ?ISpecifier $specifier = null)
    {
        $this->key = $key;
        $this->values = $values;
        $this->specifier = $specifier;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getValueInternal($this->values, explode(':', $this->getKey()));
    }

    /**
     * @inheritDoc
     */
    public function getSpecifier(): ?ISpecifier
    {
        return $this->specifier;
    }
}
