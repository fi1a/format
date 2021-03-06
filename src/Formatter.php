<?php

declare(strict_types=1);

namespace Fi1a\Format;

use ArrayAccess;
use ArrayObject;
use Fi1a\Format\Tokenizer\Token;
use Fi1a\Format\Tokenizer\Tokenizer;
use Fi1a\Tokenizer\ITokenizer;

/**
 * Форматирование строковых шаблонов
 */
class Formatter implements IFormatter
{
    /**
     * @inheritDoc
     */
    public static function format(string $string, array $values = []): string
    {
        $tokenizer = new Tokenizer($string);
        $formatted = '';
        $variables = [];
        while (($token = $tokenizer->next()) !== ITokenizer::T_EOF) {
            if ($token->getType() === Token::T_TEXT) {
                $formatted .= str_replace('%', '%%', $token->getImage());

                continue;
            }
            if ($token->getType() === Token::T_VARIABLE) {
                $formatted .= '%';
                $formatted .= array_push(
                    $variables,
                    static::getValue($values, explode(':', $token->getImage()), $token->getImage())
                );
                if ($tokenizer->lookAtNextType(2) !== Token::T_FORMAT) {
                    $formatted .= '$s';

                    continue;
                }
                $formatted .= '$' . $tokenizer->lookAtNextImage(2);
            }
        }
        array_unshift($variables, $formatted);

        return call_user_func_array('sprintf', $variables);
    }

    /**
     * Возвращает значение для замены
     *
     * @param mixed  $values
     * @param mixed[]  $vars
     *
     * @return string
     */
    private static function getValue($values, array $vars, string $fullKey)
    {
        $key = array_shift($vars);
        if (
            !is_object($values) && !(is_array($values) || $values instanceof ArrayAccess)
            || (is_object($values) && !($values instanceof ArrayObject) && !property_exists($values, $key))
            || (((is_array($values) || $values instanceof ArrayAccess)) && !array_key_exists($key, $values))
        ) {
            return '{{' . $fullKey . '}}';
        }
        if (count($vars)) {
            if (is_object($values) && !($values instanceof ArrayObject)) {
                return static::getValue($values->$key, $vars, $fullKey);
            }

            return static::getValue($values[$key], $vars, $fullKey);
        }
        if (is_object($values) && !($values instanceof ArrayObject)) {
            return static::convert($values->$key);
        }

        return static::convert($values[$key]);
    }

    /**
     * Конвертирует значение в строку
     *
     * @param mixed $value
     */
    private static function convert($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_array($value)) {
            return 'array';
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            return get_class($value);
        }
        if ($value === 0) {
            return '0';
        }

        return (string) $value;
    }
}
