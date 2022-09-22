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
