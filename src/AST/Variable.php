<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Переменная
 */
class Variable implements VariableInterface
{
    use ValueTrait;

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed[]
     */
    private $values;

    /**
     * @var SpecifierInterface[]
     */
    private $specifiers;

    /**
     * @inheritDoc
     */
    public function __construct(string $key, array $values = [], array $specifiers = [])
    {
        $this->key = $key;
        $this->values = $values;
        $this->specifiers = $specifiers;
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
    public function getSpecifiers(): array
    {
        return $this->specifiers;
    }
}
