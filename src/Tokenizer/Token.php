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

    /**
     * 'text'
     */
    public const T_TEXT = 10;

    /**
     * '{{'
     */
    public const T_OPEN_STATEMENT = 20;

    /**
     * '}}'
     */
    public const T_CLOSE_STATEMENT = 30;

    /**
     * ' '
     */
    public const T_WHITESPACE = 40;

    /**
     * 'key1:key2'
     */
    public const T_VARIABLE = 50;

    /**
     * '|'
     */
    public const T_SEPARATOR = 60;

    /**
     * 'sprintf'
     */
    public const T_SPECIFIER = 70;

    /**
     * '('
     */
    public const T_OPEN_PARENTHESES = 80;

    /**
     * ')'
     */
    public const T_CLOSE_PARENTHESES = 90;

    /**
     * '"''
     */
    public const T_QUOTE = 100;

    /**
     * ','
     */
    public const T_COMMA_SEPARATOR = 110;

    /**
     * '1.1f'
     */
    public const T_MODIFIER = 120;

    /**
     * 'if'
     */
    public const T_IF = 130;

    /**
     * 'endif'
     */
    public const T_ENDIF = 140;

    /**
     * 'key1:key2'
     */
    public const T_CONDITION_PART = 150;

    /**
     * '&&'
     */
    public const T_AND = 160;

    /**
     * '||'
     */
    public const T_OR = 170;

    /**
     * '=='
     */
    public const T_IS_EQUAL = 180;

    /**
     * '==='
     */
    public const T_IS_IDENTICAL = 190;

    /**
     * '!='
     */
    public const T_IS_NOT_EQUAL = 200;

    /**
     * '!=='
     */
    public const T_IS_NOT_IDENTICAL = 210;

    /**
     * '>'
     */
    public const T_GT = 220;

    /**
     * '<'
     */
    public const T_LT = 230;

    /**
     * '>='
     */
    public const T_GTE = 240;

    /**
     * '<='
     */
    public const T_LTE = 250;

    /**
     * '!'
     */
    public const T_NOT = 260;

    /**
     * '+'
     */
    public const T_PLUS = 270;

    /**
     * '-'
     */
    public const T_MINUS = 280;

    /**
     * '/'
     */
    public const T_DIVIDE = 290;

    /**
     * '*'
     */
    public const T_MULTIPLY = 300;

    /**
     * '('
     */
    public const T_OPEN_PARENTHESES_CONDITION = 310;

    /**
     * ')'
     */
    public const T_CLOSE_PARENTHESES_CONDITION = 320;

    /**
     * ' and '
     */
    public const T_LOGICAL_AND = 330;

    /**
     * ' or '
     */
    public const T_LOGICAL_OR = 340;

    /**
     * '%'
     */
    public const T_MOD = 350;

    /**
     * '.'
     */
    public const T_CONCAT = 360;

    /**
     * 'true'
     */
    public const T_TRUE = 370;

    /**
     * 'false'
     */
    public const T_FALSE = 380;

    /**
     * 'null'
     */
    public const T_NULL = 390;

    /**
     * 'else'
     */
    public const T_ELSE = 400;

    /**
     * 'elseif'
     */
    public const T_ELSEIF = 410;

    /**
     * @var array
     */
    protected static $types = [
        self::T_UNKNOWN_TOKEN_TYPE,
        self::T_TEXT,
        self::T_OPEN_STATEMENT,
        self::T_CLOSE_STATEMENT,
        self::T_WHITESPACE,
        self::T_VARIABLE,
        self::T_SEPARATOR,
        self::T_SPECIFIER,
        self::T_OPEN_PARENTHESES,
        self::T_CLOSE_PARENTHESES,
        self::T_QUOTE,
        self::T_COMMA_SEPARATOR,
        self::T_MODIFIER,
        self::T_IF,
        self::T_ENDIF,
        self::T_CONDITION_PART,
        self::T_AND,
        self::T_OR,
        self::T_IS_EQUAL,
        self::T_IS_IDENTICAL,
        self::T_IS_NOT_EQUAL,
        self::T_IS_NOT_IDENTICAL,
        self::T_GT,
        self::T_LT,
        self::T_GTE,
        self::T_LTE,
        self::T_NOT,
        self::T_PLUS,
        self::T_MINUS,
        self::T_DIVIDE,
        self::T_MULTIPLY,
        self::T_OPEN_PARENTHESES_CONDITION,
        self::T_CLOSE_PARENTHESES_CONDITION,
        self::T_LOGICAL_AND,
        self::T_LOGICAL_OR,
        self::T_MOD,
        self::T_CONCAT,
        self::T_TRUE,
        self::T_FALSE,
        self::T_NULL,
        self::T_ELSE,
        self::T_ELSEIF,
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
