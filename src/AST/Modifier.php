<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Модификатор спецификатора
 */
class Modifier implements IModifier
{
    use TValue;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed[]
     */
    private $values;

    /**
     * @var bool
     */
    private $isVariable;

    /**
     * @inheritDoc
     */
    public function __construct($value, array $values, bool $isVariable)
    {
        $this->value = $value;
        $this->values = $values;
        $this->isVariable = $isVariable;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->isVariable) {
            return $this->getValueInternal($this->values, explode(':', (string) $this->value));
        }

        return $this->value;
    }
}
