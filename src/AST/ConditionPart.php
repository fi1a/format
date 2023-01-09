<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use Fi1a\Format\AST\Exception\NotFoundKey;

use const ENT_COMPAT;

/**
 * Часть условия
 */
class ConditionPart extends Modifier implements ConditionPartInterface
{
    /**
     * @var SpecifierInterface[]
     */
    private $specifiers;

    /**
     * @param mixed   $value
     * @param mixed[] $modifierValues
     * @param SpecifierInterface[] $specifiers
     */
    public function __construct($value, array $modifierValues, bool $isVariable, array $specifiers = [])
    {
        parent::__construct($value, $modifierValues, $isVariable);
        $this->specifiers = $specifiers;
    }

    /**
     * @inheritDoc
     */
    public function getSpecifiers(): array
    {
        return $this->specifiers;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->isVariable) {
            try {
                /** @var mixed $value */
                $value = $this->getValueInternal($this->modifierValues, $this->explodePath((string) $this->value));
                if (is_string($value)) {
                    $value = htmlspecialchars($value, ENT_COMPAT);
                }

                return $this->applySpecifier($value, $this->getSpecifiers());
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
