<?php

declare(strict_types=1);

namespace Fi1a\Format\Tokenizer;

use Fi1a\Tokenizer\AToken;

/**
 * Токен
 */
class Token extends AToken
{
    /**
     * Неизвестный токен
     */
    public const T_UNKNOWN_TOKEN_TYPE = 1;

    public const T_TEXT = 10;

    public const T_OPEN = 20;

    public const T_VARIABLE = 30;

    public const T_FORMAT = 40;

    public const T_CLOSE = 50;

    public const T_SEPARATOR = 60;

    public const T_IF = 70;

    public const T_ELSEIF = 80;

    public const T_ELSE = 90;

    public const T_ENDIF = 100;

    public const T_OPEN_PARENTHESES = 110;

    public const T_CLOSE_PARENTHESES = 120;

    public const T_CONDITION = 130;

    /**
     * @var array
     */
    protected static $types = [
        self::T_UNKNOWN_TOKEN_TYPE,
        self::T_TEXT,
        self::T_OPEN,
        self::T_VARIABLE,
        self::T_FORMAT,
        self::T_CLOSE,
        self::T_SEPARATOR,
        self::T_IF,
        self::T_ELSEIF,
        self::T_ELSE,
        self::T_ENDIF,
        self::T_OPEN_PARENTHESES,
        self::T_CLOSE_PARENTHESES,
        self::T_CONDITION,
    ];

    /**
     * Возвращает доступные типы токенов
     *
     * @return int[]
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    protected function getTypes(): array
    {
        return static::$types;
    }
}
