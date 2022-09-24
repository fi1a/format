<?php

declare(strict_types=1);

namespace Fi1a\Format\Tokenizer;

use Fi1a\Tokenizer\ATokenizer;
use Fi1a\Tokenizer\IToken;
use Fi1a\Tokenizer\ITokenFactory;

use const PHP_EOL;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

/**
 * Лексический анализатор
 */
class Tokenizer extends ATokenizer
{
    /**
     * @inheritDoc
     * @psalm-suppress MixedAssignment
     */
    protected function tokenize(): void
    {
        $source = $this->getSource();
        $pos = 0;
        $endLine = 1;
        $startLine = 1;
        $startColumn = 1;
        $endColumn = 1;
        $tokens = [];
        preg_match_all(
            '#(\{\{[\s\t\n]*)([0-9\w\_\:]*)([\s\t\n]*\|[\s\t\n]*|)([^\}\s\t\n]*)([\s\t\n]*\}\})#',
            $source,
            $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        );
        static $types = [
            1 => Token::T_OPEN,
            2 => Token::T_VARIABLE,
            3 => Token::T_SEPARATOR,
            4 => Token::T_FORMAT,
            5 => Token::T_CLOSE,
        ];
        /**
         * @var int[] $conditionalSatements
         */
        static $conditionalSatements = [
            'if' => Token::T_IF,
            'elseif' => Token::T_ELSEIF,
            'else' => Token::T_ELSE,
            'endif' => Token::T_ENDIF,
        ];

        foreach ($matches as $match) {
            $image = substr($source, $pos, $match[1][1] - $pos);
            $pos = $match[1][1] + strlen($match[0][0]);
            if ($image) {
                $tokens[] = $this->getToken(Token::T_TEXT, $image, $startLine, $endLine, $startColumn, $endColumn);
            }
            foreach ($types as $index => $type) {
                /**
                 * @var int $index
                 * @var int $type
                 */
                if (!array_key_exists($index, $match) || $match[$index][0] === '') {
                    continue;
                }

                if (
                    $type === Token::T_VARIABLE
                    && array_key_exists(mb_strtolower($match[$index][0]), $conditionalSatements)
                ) {
                    $type = $conditionalSatements[mb_strtolower($match[$index][0])];
                }

                if ($type === Token::T_FORMAT) {
                    $formatMatch = [];
                    if (
                        preg_match(
                            '/^([0-9\w\_\-]*)(\(+)([0-9\w\_\-\:]+)(\)+)$/mui',
                            $match[$index][0],
                            $formatMatch
                        ) !== false
                        && count($formatMatch)
                    ) {
                        $tokens[] = $this->getToken(
                            Token::T_OPEN_PARENTHESES,
                            $formatMatch[2],
                            $startLine,
                            $endLine,
                            $startColumn,
                            $endColumn
                        );
                        $tokens[] = $this->getToken(
                            Token::T_CONDITION,
                            $formatMatch[3],
                            $startLine,
                            $endLine,
                            $startColumn,
                            $endColumn
                        );
                        $tokens[] = $this->getToken(
                            Token::T_CLOSE_PARENTHESES,
                            $formatMatch[4],
                            $startLine,
                            $endLine,
                            $startColumn,
                            $endColumn
                        );

                        continue;
                    }
                }

                $tokens[] = $this->getToken(
                    $type,
                    $match[$index][0],
                    $startLine,
                    $endLine,
                    $startColumn,
                    $endColumn
                );
            }
        }
        if ($pos < strlen($source)) {
            $tokens[] = $this->getToken(
                Token::T_TEXT,
                substr($source, $pos),
                $startLine,
                $endLine,
                $startColumn,
                $endColumn
            );
        }
        $this->setCount(count($tokens))
            ->setTokens($tokens);
    }

    /**
     * Формирует токен
     */
    private function getToken(
        int $type,
        string $image,
        int &$startLine,
        int &$endLine,
        int &$startColumn,
        int &$endColumn
    ): IToken {
        /**
         * @var ITokenFactory $factory
         */
        $factory = static::getTokenFactory();
        $endColumn = $startColumn + mb_strlen($image);
        $endLine += mb_substr_count($image, PHP_EOL);
        $token = $factory::factory($type, $image, $startLine, $endLine, $startColumn, $endColumn);
        $startLine = $endLine;
        $startColumn = $endColumn;

        return $token;
    }

    /**
     * @inheritDoc
     */
    public static function getTokenFactory()
    {
        return TokenFactory::class;
    }
}
