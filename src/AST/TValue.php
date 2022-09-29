<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use ArrayAccess;
use ArrayObject;
use Fi1a\Format\AST\Exception\NotFoundKey;

/**
 * Методы работы со значением
 */
trait TValue
{
    /**
     * Возвращает значение для замены
     *
     * @param mixed  $values
     * @param string[]  $vars
     *
     * @return mixed
     *
     * @psalm-suppress PossiblyInvalidArrayAccess
     */
    protected function getValueInternal($values, array $vars)
    {
        $key = array_shift($vars);
        if (
            (!is_object($values) && !(is_array($values) || $values instanceof ArrayAccess))
            || (is_object($values) && !($values instanceof ArrayObject) && !property_exists($values, $key))
            || (((is_array($values) || $values instanceof ArrayAccess)) && !array_key_exists($key, (array) $values))
        ) {
            throw new NotFoundKey('Key is not found');
        }
        if (count($vars)) {
            if (is_object($values) && !($values instanceof ArrayObject)) {
                return static::getValueInternal($values->$key, $vars);
            }

            return static::getValueInternal($values[$key], $vars);
        }
        if (is_object($values) && !($values instanceof ArrayObject)) {
            return $values->$key;
        }

        return $values[$key];
    }

    /**
     * Разделение на ключи
     *
     * @return string[]
     */
    protected function explodePath(string $path): array
    {
        $current = -1;
        $index = 0;
        $paths = [];
        do {
            $current++;
            $symbol = mb_substr($path, $current, 1);
            $prevSymbol = mb_substr($path, $current - 1, 1);

            if ($symbol === ':' && $prevSymbol !== '\\') {
                $index++;

                continue;
            }
            if (!isset($paths[$index])) {
                $paths[$index] = '';
            }
            if ($symbol === ':' && $prevSymbol === '\\') {
                $paths[$index] = mb_substr($paths[$index], 0, -1);
            }
            /**
             * @psalm-suppress PossiblyUndefinedArrayOffset
             */
            $paths[$index] .= $symbol;
        } while ($current < mb_strlen($path));

        return $paths;
    }
}
