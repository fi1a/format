<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Часть условия
 */
interface ConditionPartInterface extends ModifierInterface
{
    /**
     * @return SpecifierInterface[]
     */
    public function getSpecifiers(): array;
}
