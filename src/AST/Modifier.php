<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use Fi1a\Format\AST\Exception\NotFoundKey;

/**
 * Модификатор спецификатора
 */
class Modifier implements ModifierInterface
{
    use ValueTrait;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed[]
     */
    protected $modifierValues;

    /**
     * @var bool
     */
    protected $isVariable;

    /**
     * @inheritDoc
     */
    public function __construct($value, array $modifierValues, bool $isVariable)
    {
        $this->value = $value;
        $this->modifierValues = $modifierValues;
        $this->isVariable = $isVariable;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->isVariable) {
            try {
                return $this->getValueInternal($this->modifierValues, $this->explodePath((string) $this->value));
            } catch (NotFoundKey $exception) {
                if (is_numeric($this->value)) {
                    return $this->value;
                }

                throw $exception;
            }
        }

        return $this->value;
    }
}
