<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use const ENT_COMPAT;

/**
 * Преобразует специальные HTML-сущности обратно в соответствующие символы
 */
class UnEscape implements SpecifierInterface
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 1) {
            throw new \InvalidArgumentException('More than one modifier passed');
        }

        /**
         * @var int $flags
         */
        $flags = isset($modifiers[0]) ? (int) $modifiers[0] : null;
        if (!$flags) {
            $flags = ENT_COMPAT;
        }

        return htmlspecialchars_decode((string) $value, $flags);
    }
}
