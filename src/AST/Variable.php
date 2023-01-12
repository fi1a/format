<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use const ENT_COMPAT;

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
    public function getValue(bool $escape = true): string
    {
        $value = static::convert($this->getValueInternal($this->values, $this->explodePath($this->getKey())));
        if ($escape) {
            $value = htmlspecialchars($value, ENT_COMPAT);
        }

        return (string) $this->applySpecifier($value, $this->getSpecifiers());
    }

    /**
     * @inheritDoc
     */
    public function getSpecifiers(): array
    {
        return $this->specifiers;
    }
}
