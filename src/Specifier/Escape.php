<?php

declare(strict_types=1);

namespace Fi1a\Format\Specifier;

use const ENT_COMPAT;

/**
 * Преобразует специальные символы в HTML-сущности
 */
class Escape implements SpecifierInterface
{
    /**
     * @inheritDoc
     */
    public function format($value, ...$modifiers): string
    {
        if (count($modifiers) > 3) {
            throw new \InvalidArgumentException('More than three modifier passed');
        }

        /**
         * @var int $flags
         */
        $flags = isset($modifiers[0]) ? (int) $modifiers[0] : null;
        if (!$flags) {
            $flags = ENT_COMPAT;
        }
        $encoding = isset($modifiers[1]) ? (string) $modifiers[1] : 'UTF-8';
        $doubleEncode = !isset($modifiers[2]) || $modifiers[2] === 'true';

        return htmlspecialchars((string) $value, $flags, $encoding, $doubleEncode);
    }
}
