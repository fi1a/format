<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Переменная
 */
class Variable implements VariableInterface
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
     * @var SpecifierInterface|null
     */
    private $specifier;

    /**
     * @inheritDoc
     */
    public function __construct(string $key, array $values = [], ?SpecifierInterface $specifier = null)
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
        return $this->getValueInternal($this->values, $this->explodePath($this->getKey()));
    }

    /**
     * @inheritDoc
     */
    public function getSpecifier(): ?SpecifierInterface
    {
        return $this->specifier;
    }
}
