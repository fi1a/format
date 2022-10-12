<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Tokenizer;

use Fi1a\Format\Tokenizer\Token;
use Fi1a\Format\Tokenizer\Tokenizer;
use Fi1a\Tokenizer\ITokenizer;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование лексического анализатора
 */
class TokenizerTest extends TestCase
{
    /**
     * Данные для тестирования лексического анализатора
     *
     * @return mixed[]
     */
    public function dataTokenizer(): array
    {
        return [
            // 0
            [
                '{{}}',
                3,
                ['{{', '', '}}',],
                [Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,],
            ],
            // 1
            [
                '{{}} {{}}',
                7,
                ['{{', '', '}}', ' ', '{{', '', '}}'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 2
            [
                '{{key1:key2}} {{key3:key4}}',
                7,
                ['{{', 'key1:key2', '}}', ' ', '{{', 'key3:key4', '}}'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 3
            [
                '{{  key1:key2  }} {{  key3:key4  }}',
                11,
                ['{{', '  ', 'key1:key2', '  ', '}}', ' ', '{{', '  ', 'key3:key4', '  ', '}}'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_VARIABLE, Token::T_WHITESPACE,
                    Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_WHITESPACE,
                    Token::T_VARIABLE, Token::T_WHITESPACE, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 4
            [
                '{{   }}',
                4,
                ['{{', '   ', '', '}}',],
                [Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT,],
            ],
            // 5
            [
                '{{|sprintf}}',
                5,
                ['{{', '', '|', 'sprintf', '}}',],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 6
            [
                '{{  key1:key2  |  sprintf  }}',
                9,
                ['{{', '  ', 'key1:key2', '  ', '|', '  ', 'sprintf', '  ', '}}',],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_VARIABLE, Token::T_WHITESPACE,
                    Token::T_SEPARATOR, Token::T_WHITESPACE, Token::T_SPECIFIER, Token::T_WHITESPACE,
                    Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 7
            [
                '{{|sprintf()}}',
                7,
                ['{{', '', '|', 'sprintf', '(', ')', '}}',],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 8
            [
                '{{|sprintf("1, 2", key1:key2)}}',
                13,
                ['{{', '', '|', 'sprintf', '(', '"', '1, 2', '"', ',', ' ', 'key1:key2', ')', '}}',],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_COMMA_SEPARATOR, Token::T_WHITESPACE, Token::T_MODIFIER,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 9
            [
                '{{',
                2,
                ['{{', ''],
                [Token::T_OPEN_STATEMENT, Token::T_VARIABLE],
            ],
            // 10
            [
                '{{key1:key2',
                2,
                ['{{', 'key1:key2'],
                [Token::T_OPEN_STATEMENT, Token::T_VARIABLE],
            ],
            // 11
            [
                '{{key1:key2|',
                3,
                ['{{', 'key1:key2', '|'],
                [Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR,],
            ],
            // 12
            [
                '{{key1:key2|sprintf',
                4,
                ['{{', 'key1:key2', '|', 'sprintf'],
                [Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,],
            ],
            // 13
            [
                '{{key1:key2|sprintf(',
                5,
                ['{{', 'key1:key2', '|', 'sprintf', '('],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES,
                ],
            ],
            // 14
            [
                '{{key1:key2|sprintf("',
                6,
                ['{{', 'key1:key2', '|', 'sprintf', '(', '"'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                ],
            ],
            // 15
            [
                '{{key1:key2|sprintf(" 123',
                7,
                ['{{', 'key1:key2', '|', 'sprintf', '(', '"', ' 123'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER,
                ],
            ],
            // 16
            [
                '{{key1:key2|sprintf(" 123"',
                8,
                ['{{', 'key1:key2', '|', 'sprintf', '(', '"', ' 123', '"'],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                ],
            ],
            // 17
            [
                '{{key1:key2|sprintf(" 123", ',
                10,
                ['{{', 'key1:key2', '|', 'sprintf', '(', '"', ' 123', '"', ',', ' '],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_COMMA_SEPARATOR, Token::T_WHITESPACE,
                ],
            ],
            // 18
            [
                '{{key1:key2|sprintf}} {{key3:key4|sprintf(key1:key2)}}',
                14,
                [
                    '{{', 'key1:key2', '|', 'sprintf', '}}', ' ', '{{', 'key3:key4', '|', 'sprintf',
                    '(', 'key1:key2', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_MODIFIER, Token::T_CLOSE_PARENTHESES,
                    Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 19
            [
                '{{ if( key1:key2 ) }}text{{endif}}',
                14,
                [
                    '{{', ' ', 'if', '(', ' ', 'key1:key2', ' ', ')', ' ', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_CLOSE_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 20
            [
                '{{ if( key1:key2 && "key1:key2" ) }}text{{endif}}',
                20,
                [
                    '{{', ' ', 'if', '(', ' ', 'key1:key2', ' ', '&&', ' ', '"', 'key1:key2', '"', ' ', ')',
                    ' ', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_AND, Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART,
                    Token::T_QUOTE, Token::T_WHITESPACE, Token::T_CLOSE_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 21
            [
                '{{ if( key1:key2 || "key1:key2" ) }}text{{endif}}',
                20,
                [
                    '{{', ' ', 'if', '(', ' ', 'key1:key2', ' ', '||', ' ', '"', 'key1:key2', '"', ' ', ')',
                    ' ', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_OR, Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART,
                    Token::T_QUOTE, Token::T_WHITESPACE, Token::T_CLOSE_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 22
            [
                '{{ if( key1:key2 or "key1:key2" ) }}text{{endif}}',
                20,
                [
                    '{{', ' ', 'if', '(', ' ', 'key1:key2', ' ', 'or', ' ', '"', 'key1:key2', '"', ' ', ')',
                    ' ', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_LOGICAL_OR, Token::T_WHITESPACE, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_CLOSE_PARENTHESES,
                    Token::T_WHITESPACE, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 23
            [
                '{{if(key1:key2orkey1:key3 || \' key1:key4 \')}}text{{endif}}',
                16,
                [
                    '{{', 'if', '(', 'key1:key2orkey1:key3', ' ', '||', ' ', '\'', ' key1:key4 ',
                    '\'', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_OR, Token::T_WHITESPACE, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 24
            [
                '{{if(key1:key2 && key1:key3 || \' key1:key4 \')}}text{{endif}}',
                20,
                [
                    '{{', 'if', '(', 'key1:key2', ' ', '&&', ' ', 'key1:key3', ' ', '||', ' ', '\'', ' key1:key4 ',
                    '\'', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_AND, Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_OR, Token::T_WHITESPACE, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 25
            [
                '{{if(key1:key2 and key1:key3 || \' key1:key4 \')}}text{{endif}}',
                20,
                [
                    '{{', 'if', '(', 'key1:key2', ' ', 'and', ' ', 'key1:key3', ' ', '||', ' ', '\'', ' key1:key4 ',
                    '\'', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_LOGICAL_AND, Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_OR, Token::T_WHITESPACE, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 26
            [
                '{{if(key1:key2andkey1:key3 || \' key1:key4 \')}}text{{endif}}',
                16,
                [
                    '{{', 'if', '(', 'key1:key2andkey1:key3', ' ', '||', ' ', '\'', ' key1:key4 ',
                    '\'', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_WHITESPACE, Token::T_OR, Token::T_WHITESPACE, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                    Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 27
            [
                '{{if("1" === "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '===', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_IS_IDENTICAL,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 28
            [
                '{{if("1" == "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '==', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_IS_EQUAL,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 29
            [
                '{{if("1" !== "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '!==', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_IS_NOT_IDENTICAL,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 30
            [
                '{{if("1" != "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '!=', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_IS_NOT_EQUAL,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 31
            [
                '{{if("1" > "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '>', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_GT,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 32
            [
                '{{if("1" >= "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '>=', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_GTE,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 33
            [
                '{{if("1" < "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '<', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_LT,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 34
            [
                '{{if("1" <= "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '1', '"', ' ', '<=', ' ', '"', '2', '"', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_LTE,
                    Token::T_WHITESPACE, Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 35
            [
                '{{if(!false)}}text{{endif}}',
                11,
                [
                    '{{', 'if', '(', '!', 'false', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_NOT,
                    Token::T_FALSE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 36
            [
                '{{if(true&&false)}}text{{endif}}',
                12,
                [
                    '{{', 'if', '(', 'true', '&&', 'false', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_AND, Token::T_FALSE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 37
            [
                '{{if(null||false)}}text{{endif}}',
                12,
                [
                    '{{', 'if', '(', 'null', '||', 'false', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_NULL,
                    Token::T_OR, Token::T_FALSE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 38
            [
                '{{if(2 + 2)}}text{{endif}}',
                14,
                [
                    '{{', 'if', '(', '2', ' ', '+', ' ', '2', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_PLUS, Token::T_WHITESPACE,
                    Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 39
            [
                '{{if(2 - 2)}}text{{endif}}',
                14,
                [
                    '{{', 'if', '(', '2', ' ', '-', ' ', '2', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_MINUS, Token::T_WHITESPACE,
                    Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 40
            [
                '{{if(2 / 2)}}text{{endif}}',
                14,
                [
                    '{{', 'if', '(', '2', ' ', '/', ' ', '2', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_DIVIDE, Token::T_WHITESPACE,
                    Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 41
            [
                '{{if(2 % 2)}}text{{endif}}',
                14,
                [
                    '{{', 'if', '(', '2', ' ', '%', ' ', '2', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_MOD, Token::T_WHITESPACE,
                    Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 42
            [
                '{{if("2" . "2")}}text{{endif}}',
                18,
                [
                    '{{', 'if', '(', '"', '2', '"', ' ', '.', ' ', '"', '2', '"', ')', '}}',
                    'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_WHITESPACE, Token::T_CONCAT, Token::T_WHITESPACE,
                    Token::T_QUOTE, Token::T_CONDITION_PART, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES,
                    Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_ENDIF,
                    Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 43
            [
                '{{if(2 * 2)}}text{{endif}}',
                14,
                [
                    '{{', 'if', '(', '2', ' ', '*', ' ', '2', ')', '}}', 'text', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_CONDITION_PART, Token::T_WHITESPACE, Token::T_MULTIPLY, Token::T_WHITESPACE,
                    Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 44
            [
                '{{if((1 + 2) == 3)}}text{{endif}}',
                20,
                [
                    '{{', 'if', '(', '(', '1', ' ', '+', ' ', '2', ')', ' ', '==', ' ', '3', ')', '}}', 'text',
                    '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES,
                    Token::T_OPEN_PARENTHESES_CONDITION, Token::T_CONDITION_PART, Token::T_WHITESPACE,
                    Token::T_PLUS, Token::T_WHITESPACE, Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES_CONDITION,
                    Token::T_WHITESPACE, Token::T_IS_EQUAL, Token::T_WHITESPACE, Token::T_CONDITION_PART,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 45
            [
                '{{if(true)}}text1{{elseif(false)}}text2{{else}}text3{{endif}}',
                21,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text1', '{{', 'elseif', '(', 'false', ')', '}}',
                    'text2', '{{', 'else', '}}', 'text3', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ELSEIF, Token::T_OPEN_PARENTHESES, Token::T_FALSE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ELSE, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 46
            [
                '{{if(true',
                4,
                [
                    '{{', 'if', '(', 'true',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                ],
            ],
            // 47
            [
                '{{if(true)',
                5,
                [
                    '{{', 'if', '(', 'true', ')',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES,
                ],
            ],
            // 48
            [
                '{{if(true)}',
                6,
                [
                    '{{', 'if', '(', 'true', ')', '}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_TEXT,
                ],
            ],
            // 49
            [
                '{{if(true)}}text{',
                7,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text{',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                ],
            ],
            // 50
            [
                '{{if(true)}}text{{',
                9,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text', '{{', '',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_VARIABLE,
                ],
            ],
            // 51
            [
                '{{if(true)}}text{{else',
                9,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text', '{{', 'else',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ELSE,
                ],
            ],
            // 52
            [
                '{{if(true)}}text{{else}}',
                10,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text', '{{', 'else', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ELSE, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 53
            [
                '{{if(true)}}text{{else}}text',
                11,
                [
                    '{{', 'if', '(', 'true', ')', '}}', 'text', '{{', 'else', '}}', 'text',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ELSE, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                ],
            ],
            // 54
            [
                '{{IF(TRUE)}}text{{ELSE}}text{{ENDIF',
                13,
                [
                    '{{', 'IF', '(', 'TRUE', ')', '}}', 'text', '{{', 'ELSE', '}}', 'text', '{{', 'ENDIF',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_TRUE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ELSE, Token::T_CLOSE_STATEMENT, Token::T_TEXT, Token::T_OPEN_STATEMENT,
                    Token::T_ENDIF,
                ],
            ],
            // 55
            [
                '"text{{key1:key2}}text"',
                5,
                [
                    '"text', '{{', 'key1:key2', '}}', 'text"',
                ],
                [
                    Token::T_TEXT, Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                ],
            ],
            // 56
            [
                '"text\{\{key1:key2\}\}text"',
                1,
                [
                    '"text\{\{key1:key2\}\}text"',
                ],
                [
                    Token::T_TEXT,
                ],
            ],
            // 57
            [
                '"text{\{key1:key2}\}text"',
                1,
                [
                    '"text{\{key1:key2}\}text"',
                ],
                [
                    Token::T_TEXT,
                ],
            ],
            // 58
            [
                "{{array:key2|sprintf('d')}}, {{\n array:key1 \t |sprintf('1.1f')}}",
                23,
                [
                    '{{', 'array:key2', '|', 'sprintf', '(', '\'', 'd', '\'', ')', '}}', ', ', '{{', "\n ",
                    'array:key1', " \t ", '|', 'sprintf', '(', '\'', '1.1f', '\'', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT, Token::T_TEXT,
                    Token::T_OPEN_STATEMENT, Token::T_WHITESPACE, Token::T_VARIABLE, Token::T_WHITESPACE,
                    Token::T_SEPARATOR, Token::T_SPECIFIER, Token::T_OPEN_PARENTHESES, Token::T_QUOTE,
                    Token::T_MODIFIER, Token::T_QUOTE, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 59
            [
                "{{0|sprintf('\'.9d')}}",
                10,
                [
                    '{{', '0', '|', 'sprintf', '(', '\'', '\\\'.9d', '\'', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 60
            [
                '{{|sprintf(true, false, null, key1:key2)}}',
                17,
                [
                    '{{', '', '|', 'sprintf', '(', 'true', ',', ' ', 'false', ',', ' ', 'null', ',', ' ', 'key1:key2',
                    ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_TRUE, Token::T_COMMA_SEPARATOR, Token::T_WHITESPACE,
                    Token::T_FALSE, Token::T_COMMA_SEPARATOR, Token::T_WHITESPACE, Token::T_NULL,
                    Token::T_COMMA_SEPARATOR, Token::T_WHITESPACE, Token::T_MODIFIER, Token::T_CLOSE_PARENTHESES,
                    Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 61
            [
                '{{if(key1:key2&&key1:key2)}}{{endif}}',
                11,
                [
                    '{{', 'if', '(', 'key1:key2', '&&', 'key1:key2', ')', '}}', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_AND, Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES,
                    Token::T_CLOSE_STATEMENT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 62
            [
                '{{if(key1:key2&&key1:key2)}}{{endif}}',
                11,
                [
                    '{{', 'if', '(', 'key1:key2', '&&', 'key1:key2', ')', '}}', '{{', 'endif', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_IF, Token::T_OPEN_PARENTHESES, Token::T_CONDITION_PART,
                    Token::T_AND, Token::T_CONDITION_PART, Token::T_CLOSE_PARENTHESES,
                    Token::T_CLOSE_STATEMENT, Token::T_OPEN_STATEMENT, Token::T_ENDIF, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 63
            [
                '{{key1}',
                3,
                [
                    '{{', 'key1', '}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_TEXT,
                ],
            ],
            // 64
            [
                '{{|sprintf(key1 , key2)}}',
                12,
                [
                    '{{', '', '|', 'sprintf', '(', 'key1', ' ', ',', ' ', 'key2', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_MODIFIER, Token::T_WHITESPACE, Token::T_COMMA_SEPARATOR,
                    Token::T_WHITESPACE, Token::T_MODIFIER, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 65
            [
                '{{|sprintf(key1,key2)}}',
                10,
                [
                    '{{', '', '|', 'sprintf', '(', 'key1', ',', 'key2', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_MODIFIER, Token::T_COMMA_SEPARATOR,
                    Token::T_MODIFIER, Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 66
            [
                '{{0|sprintf("\'.09d")}}',
                10,
                [
                    '{{', '0', '|', 'sprintf', '(', '"', '\'.09d', '"', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
            // 67
            [
                '{{0|sprintf("0")}}',
                10,
                [
                    '{{', '0', '|', 'sprintf', '(', '"', '0', '"', ')', '}}',
                ],
                [
                    Token::T_OPEN_STATEMENT, Token::T_VARIABLE, Token::T_SEPARATOR, Token::T_SPECIFIER,
                    Token::T_OPEN_PARENTHESES, Token::T_QUOTE, Token::T_MODIFIER, Token::T_QUOTE,
                    Token::T_CLOSE_PARENTHESES, Token::T_CLOSE_STATEMENT,
                ],
            ],
        ];
    }

    /**
     * Тестирование лексического анализатора
     *
     * @param string[] $images
     * @param int[] $types
     *
     * @dataProvider dataTokenizer
     */
    public function testTokenizer(string $source, int $count, array $images, array $types): void
    {
        $tokenizer = new Tokenizer($source);
        $imagesEquals = [];
        $typesEquals = [];
        $image = '';
        while (($token = $tokenizer->next()) !== ITokenizer::T_EOF) {
            $imagesEquals[] = $token->getImage();
            $typesEquals[] = $token->getType();
            $image .= $token->getImage();
        }

        $this->assertEquals($images, $imagesEquals);
        $this->assertEquals($types, $typesEquals);
        $this->assertEquals($source, $image);
        $this->assertEquals($count, $tokenizer->getCount());
    }
}
