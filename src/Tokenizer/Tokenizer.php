<?php

declare(strict_types=1);

namespace Fi1a\Format\Tokenizer;

use Fi1a\Tokenizer\AParseFunction;
use Fi1a\Tokenizer\IToken;

/**
 * Лексический анализатор
 */
class Tokenizer extends AParseFunction
{
    /**
     * @var string
     */
    protected $whiteSpaceReturn = '';

    /**
     * @var string
     */
    protected $quoteReturn = '';

    /**
     * @var string
     */
    protected $commaSeparatorReturn = '';

    /**
     * @var string
     */
    protected $openParenthesesReturn = '';

    /**
     * @var int
     */
    protected $openParenthesesCount = 0;

    /**
     * @var int[]
     */
    protected static $keywords = [
        'if' => Token::T_IF,
        'endif' => Token::T_ENDIF,
        'elseif' => Token::T_ELSEIF,
        'else' => Token::T_ELSE,
    ];

    /**
     * @var int[]
     */
    protected static $operators = [
        'false' => Token::T_FALSE,
        'true' => Token::T_TRUE,
        'null' => Token::T_NULL,
        'and' => Token::T_LOGICAL_AND,
        '===' => Token::T_IS_IDENTICAL,
        '!==' => Token::T_IS_NOT_IDENTICAL,
        'or' => Token::T_LOGICAL_OR,
        '>=' => Token::T_GTE,
        '<=' => Token::T_LTE,
        '&&' => Token::T_AND,
        '||' => Token::T_OR,
        '==' => Token::T_IS_EQUAL,
        '!=' => Token::T_IS_NOT_EQUAL,
        '>' => Token::T_GT,
        '<' => Token::T_LT,
        '!' => Token::T_NOT,
        '+' => Token::T_PLUS,
        '-' => Token::T_MINUS,
        '*' => Token::T_MULTIPLY,
        '/' => Token::T_DIVIDE,
        '%' => Token::T_MOD,
        '.' => Token::T_CONCAT,
    ];

    /**
     * @var int[]
     */
    protected static $values = [
        'false' => Token::T_FALSE,
        'true' => Token::T_TRUE,
        'null' => Token::T_NULL,
    ];

    /**
     * @inheritDoc
     */
    public function __construct(string $source, ?string $encoding = null)
    {
        $this->setParseFunction('parse');
        parent::__construct($source, $encoding);
    }

    /**
     * Базовая функция парсинга
     *
     * @param IToken[]    $tokens
     */
    protected function parse(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        do {
            $current++;
            if (!$source || $current >= mb_strlen($source)) {
                $finish = true;

                return;
            }

            $symbol = mb_substr($source, $current, 1);
            $secondSymbol = mb_substr($source, $current + 1, 1);

            if ($symbol === '{' && $secondSymbol === '{') {
                $this->setParseFunction('parseOpenStatement');

                return;
            }
            if ($symbol === '}' && $secondSymbol === '}') {
                $this->setParseFunction('parseCloseStatement');

                return;
            }

            $type = Token::T_TEXT;
            $image .= $symbol;
        } while (true);
    }

    /**
     * Парсинг закрытия оператора
     *
     *            --
     * {{key1:key2}}
     *            --
     *
     * @param IToken[] $tokens
     */
    protected function parseCloseStatement(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $secondSymbol = mb_substr($source, $current + 1, 1);
        $image = $symbol . $secondSymbol;
        $type = Token::T_CLOSE_STATEMENT;
        $this->setParseFunction('parse');
        $current++;
    }

    /**
     * Парсинг открытия оператора
     *
     * --
     * {{key1:key2}}
     * --
     *
     * @param IToken[]    $tokens
     */
    protected function parseOpenStatement(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $secondSymbol = mb_substr($source, $current + 1, 1);
        $image = $symbol . $secondSymbol;
        $type = Token::T_OPEN_STATEMENT;
        $this->setParseFunction('parseStatement');
        $current += 2;
    }

    /**
     * Проверяем на ключевые слова
     */
    protected function checkKeywords(string $image, ?int &$type): void
    {
        if (array_key_exists(mb_strtolower($image), static::$keywords)) {
            $type = static::$keywords[mb_strtolower($image)];

            return;
        }
        $type = Token::T_VARIABLE;
    }

    /**
     * Парсинг оператора
     *                --
     * {{if(key1:key2 || key1:key2)}}
     *                --
     *
     * @param IToken[] $tokens
     */
    protected function parseOperator(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        foreach (static::$operators as $operator => $setType) {
            $key = mb_substr($source, $current, mb_strlen((string) $operator));
            if (mb_strtolower($key) === $operator) {
                $type = $setType;
                $image = $key;
                $current += mb_strlen($operator);

                break;
            }
        }

        $this->setParseFunction('parseConditions');
    }

    /**
     * Парсинг значения
     *
     *            ----
     * {{|sprintf(true)}}
     *            ----
     *
     * @param IToken[] $tokens
     */
    protected function parseValue(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        foreach (static::$values as $value => $setType) {
            $key = mb_substr($source, $current, mb_strlen((string) $value));
            if (mb_strtolower($key) === $value) {
                $type = $setType;
                $image = $key;
                $current += mb_strlen($value);

                break;
            }
        }

        $this->setParseFunction('parseSpecifierModifiers');
    }

    /**
     * Парсинг условия
     *      ----------------------
     * {{if(key1:key2 && key1:key2)}}
     *      ----------------------
     *
     * @param IToken[] $tokens
     */
    protected function parseConditions(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $matchRegexp = '/[\s\t\n\(\)\!\+\-\/\*\%\&\|]/mui';

        do {
            $symbol = mb_substr($source, $current, 1);
            $prevSymbol = mb_substr($source, $current - 1, 1);

            if (preg_match('/[\s\t\n]/mui', $symbol) && !$quote && !$single) {
                $this->whiteSpaceReturn = 'parseConditions';
                $this->setParseFunction('parseWhitespace');
                if ($image) {
                    $type = Token::T_CONDITION_PART;
                }

                return;
            }
            if (($symbol === '"' || $symbol === '\'') && $prevSymbol !== '\\') {
                $this->quoteReturn = 'parseConditions';
                $this->setParseFunction('parseQuote');
                if ($image) {
                    $type = Token::T_CONDITION_PART;
                }

                return;
            }

            foreach (array_keys(static::$operators) as $operator) {
                $key = mb_strtolower(mb_substr($source, $current, mb_strlen((string) $operator)));
                $nextKey = mb_substr($source, $current + mb_strlen((string) $operator), 1);
                if (
                    $key === $operator && !$quote && !$single
                    && (
                        !preg_match('/[\w]+/mui', $operator)
                        || !$nextKey
                        || (
                            preg_match($matchRegexp, $prevSymbol)
                            && preg_match($matchRegexp, $nextKey)
                        )
                    )
                ) {
                    $this->setParseFunction('parseOperator');
                    if ($image) {
                        $type = Token::T_CONDITION_PART;
                    }

                    return;
                }
            }

            if ($symbol === '(' && !$quote && !$single) {
                $this->openParenthesesCount++;
                $this->setParseFunction('parseOpenParenthesesCondition');

                return;
            }

            if ($symbol === ')' && !$quote && !$single && $this->openParenthesesCount > 1) {
                $this->openParenthesesCount--;
                $this->setParseFunction('parseCloseParenthesesCondition');
                if ($image) {
                    $type = Token::T_CONDITION_PART;
                }

                return;
            }

            $loop = ($symbol !== ')' || $quote || $single) && $current < mb_strlen($source);
            if ($loop) {
                $image .= $symbol;
            }

            $current++;
        } while ($loop);
        $current--;
        $this->openParenthesesCount--;
        $this->setParseFunction('parseCloseParentheses');
        if ($image) {
            $type = Token::T_CONDITION_PART;
        }
    }

    /**
     * Парсинг оператора
     *   ---------
     * {{key1:key2}}
     *   ---------
     *
     * @param IToken[] $tokens
     */
    protected function parseStatement(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        do {
            $symbol = mb_substr($source, $current, 1);
            $secondSymbol = mb_substr($source, $current + 1, 1);

            if (preg_match('/[\s\t\n]/mui', $symbol)) {
                $this->whiteSpaceReturn = 'parseStatement';
                $this->setParseFunction('parseWhitespace');
                if ($image) {
                    $this->checkKeywords($image, $type);
                }

                return;
            }
            if ($symbol === '|') {
                $this->setParseFunction('parseSeparator');
                if (
                    !count($tokens)
                    || (
                        (
                            !isset($tokens[count($tokens) - 1])
                            || $tokens[count($tokens) - 1]->getType() !== Token::T_VARIABLE
                        )
                        && (
                            !isset($tokens[count($tokens) - 2])
                            || $tokens[count($tokens) - 2]->getType() !== Token::T_VARIABLE
                        )
                    )
                ) {
                    $type = Token::T_VARIABLE;
                }

                return;
            }
            if ($symbol === '(') {
                $this->openParenthesesCount++;
                $this->openParenthesesReturn = 'parseConditions';
                $this->setParseFunction('parseOpenParentheses');
                if ($image) {
                    $this->checkKeywords($image, $type);
                }

                return;
            }

            if ($symbol === '}' && !$secondSymbol) {
                $current--;
                $this->setParseFunction('parse');
                if ($image) {
                    $this->checkKeywords($image, $type);
                }

                return;
            }

            $loop = ($symbol !== '}' || $secondSymbol !== '}') && $current < mb_strlen($source);
            if ($loop) {
                $image .= $symbol;
            }
            $current++;
        } while ($loop);
        $current -= 2;
        if (
            (count($tokens) && $tokens[count($tokens) - 1]->getType() === Token::T_OPEN_STATEMENT)
            || (
                count($tokens) > 1
                && $tokens[count($tokens) - 2]->getType() === Token::T_OPEN_STATEMENT
                && $tokens[count($tokens) - 1]->getType() === Token::T_WHITESPACE
            )
        ) {
            $type = Token::T_VARIABLE;
            if ($image) {
                $this->checkKeywords($image, $type);
            }
        }
        $this->setParseFunction('parse');
    }

    /**
     * Парсинг открытия скобки указания модификаторов
     *
     *                       -    -
     * {{key1:key2|specifier("1", "2")}}
     *                       -    -
     *
     * @param IToken[]    $tokens
     */
    protected function parseQuote(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $type = Token::T_QUOTE;
        $image = $symbol;
        if ($image === '"' && !$single) {
            $quote = !$quote;
        }
        if ($image === '\'' && !$quote) {
            $single = !$single;
        }
        $current++;
        $this->setParseFunction($this->quoteReturn);
    }

    /**
     * Парсинг модификаторов спецификатора
     *
     *                       ---  ---
     * {{key1:key2|specifier("1", "2")}}
     *                       ---  ---
     *
     * @param IToken[]    $tokens
     */
    protected function parseSpecifierModifiers(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $matchRegexp = '/[\s\t\n\,\(\)]/mui';
        do {
            $symbol = mb_substr($source, $current, 1);
            $prevSymbol = mb_substr($source, $current - 1, 1);

            if (preg_match('/[\s\t\n]/mui', $symbol) && !$quote && !$single) {
                $this->whiteSpaceReturn = 'parseSpecifierModifiers';
                $this->setParseFunction('parseWhitespace');
                if ($image) {
                    $type = Token::T_MODIFIER;
                }

                return;
            }
            if ($symbol === ',' && !$quote && !$single) {
                $this->commaSeparatorReturn = 'parseSpecifierModifiers';
                $this->setParseFunction('parseCommaSeparator');
                if ($image || $image === '0') {
                    $type = Token::T_MODIFIER;
                }

                return;
            }
            if (($symbol === '"' && !$single) || ($symbol === '\'' && !$quote) && $prevSymbol !== '\\') {
                $this->quoteReturn = 'parseSpecifierModifiers';
                $this->setParseFunction('parseQuote');
                if ($image) {
                    $type = Token::T_MODIFIER;
                }

                return;
            }

            foreach (array_keys(static::$values) as $value) {
                $key = mb_strtolower(mb_substr($source, $current, mb_strlen((string) $value)));
                $nextKey = mb_substr($source, $current + mb_strlen((string) $value), 1);
                if (
                    $key === $value && !$quote && !$single
                    && (
                        !$nextKey
                        || (
                            preg_match($matchRegexp, $prevSymbol)
                            && preg_match($matchRegexp, $nextKey)
                        )
                    )
                ) {
                    $this->setParseFunction('parseValue');

                    return;
                }
            }

            $loop = ($symbol !== ')' || $quote || $single) && $current < mb_strlen($source);
            if ($loop) {
                $image .= $symbol;
            }

            $current++;
        } while ($loop);
        $current--;
        $this->setParseFunction('parseCloseParentheses');
        if ($image) {
            $type = Token::T_MODIFIER;
        }
    }

    /**
     * Парсинг запятой
     *                          -
     * {{key1:key2|specifier("1", "2")}}
     *                          -
     *
     * @param IToken[]    $tokens
     */
    protected function parseCommaSeparator(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $type = Token::T_COMMA_SEPARATOR;
        $image = $symbol;
        $current++;
        $this->setParseFunction($this->commaSeparatorReturn);
    }

    /**
     * Парсинг закрытия скобки указания модификаторов
     *                               -
     * {{key1:key2|specifier("1", "2")}}
     *                               -
     *
     * @param IToken[]    $tokens
     */
    protected function parseCloseParentheses(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        if ($symbol === ')') {
            $type = Token::T_CLOSE_PARENTHESES;
            $image = $symbol;
        }
        $current++;
        $this->setParseFunction('parseStatement');
    }

    /**
     * Парсинг открытия скобки в условиях
     *
     *      -
     * {{if((1 + 2) == 3)}}
     *      -
     *
     * @param IToken[]    $tokens
     */
    protected function parseOpenParenthesesCondition(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $type = Token::T_OPEN_PARENTHESES_CONDITION;
        $image = $symbol;
        $current++;
        $this->setParseFunction('parseConditions');
    }

    /**
     * Парсинг открытия скобки в условиях
     *
     *            -
     * {{if((1 + 2) == 3)}}
     *            -
     *
     * @param IToken[]    $tokens
     */
    protected function parseCloseParenthesesCondition(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $type = Token::T_CLOSE_PARENTHESES_CONDITION;
        $image = $symbol;
        $current++;
        $this->setParseFunction('parseConditions');
    }

    /**
     * Парсинг открытия скобки указания модификаторов
     *
     *                      -
     * {{key1:key2|specifier("1", "2")}}
     *                      -
     *
     * @param IToken[]    $tokens
     */
    protected function parseOpenParentheses(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);

        if ($symbol === '(') {
            $type = Token::T_OPEN_PARENTHESES;
            $image = $symbol;
            $current++;
            $this->setParseFunction($this->openParenthesesReturn);

            return;
        }
        $this->setParseFunction('parseStatement');
    }

    /**
     * Парсинг спецификатора
     *
     *             ---------
     * {{key1:key2|specifier("1", "2")}}
     *             ---------
     *
     * @param IToken[]    $tokens
     */
    protected function parseSpecifier(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        do {
            $symbol = mb_substr($source, $current, 1);
            $secondSymbol = mb_substr($source, $current + 1, 1);
            if (preg_match('/[\s\t\n]/mui', $symbol)) {
                $this->setParseFunction('parseWhitespace');
                if ($image) {
                    $type = Token::T_SPECIFIER;
                    $this->openParenthesesReturn = 'parseSpecifierModifiers';
                    $this->whiteSpaceReturn = 'parseOpenParentheses';
                } else {
                    $this->whiteSpaceReturn = 'parseSpecifier';
                }

                return;
            }
            if ($symbol === '(') {
                $type = Token::T_SPECIFIER;
                $this->openParenthesesReturn = 'parseSpecifierModifiers';
                $this->setParseFunction('parseOpenParentheses');

                return;
            }
            $loop = ($symbol !== '}' || $secondSymbol !== '}')
                && $current < mb_strlen($source);
            if ($loop) {
                $image .= $symbol;
            }
            $current++;
        } while ($loop);
        $current--;
        if ($image) {
            $type = Token::T_SPECIFIER;
        }
        $this->setParseFunction('parseStatement');
    }

    /**
     * Парсинг разделителя спецификатора
     *
     *            -
     * {{key1:key2|specifier("1", "2")}}
     *            -
     *
     * @param IToken[]    $tokens
     */
    protected function parseSeparator(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $symbol = mb_substr($source, $current, 1);
        $type = Token::T_SEPARATOR;
        $image = $symbol;
        $current++;
        $this->setParseFunction('parseSpecifier');
    }

    /**
     * Парсинг пробела
     *   -         - -
     * {{ key1:key2 | specifier("1", "2")}}
     *   -         - -
     *
     * @param IToken[]    $tokens
     */
    protected function parseWhitespace(
        bool &$finish,
        string &$source,
        int &$current,
        string &$image,
        ?int &$type,
        array &$tokens,
        bool &$quote,
        bool &$single
    ): void {
        $type = Token::T_WHITESPACE;
        do {
            $symbol = mb_substr($source, $current, 1);
            $loop = preg_match('/[\s\t\n]/mui', $symbol) && $current < mb_strlen($source);
            if ($loop) {
                $image .= $symbol;
            }
            $current++;
        } while ($loop);
        $current--;
        $this->setParseFunction($this->whiteSpaceReturn);
    }

    /**
     * @inheritDoc
     */
    public static function getTokenFactory()
    {
        return TokenFactory::class;
    }
}
