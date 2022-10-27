<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

use Fi1a\Format\AST\Exception\FormatErrorException;
use Fi1a\Format\AST\Exception\NotFoundKey;
use Fi1a\Format\Tokenizer\Token;
use Fi1a\Format\Tokenizer\Tokenizer;
use Fi1a\Tokenizer\IToken;
use Fi1a\Tokenizer\ITokenizer;
use Fi1a\Tokenizer\PHP\Token as PHPToken;
use Fi1a\Tokenizer\PHP\TokenizerFactory;

/**
 * AST
 */
class AST implements IAST
{
    /**
     * @var INodes
     */
    protected $nodes;

    /**
     * Разрешенные токены для метода ::expression($expression)
     *
     * @var array
     */
    private static $allowPhp = [
        PHPToken::T_LNUMBER,
        PHPToken::T_BOOLEAN_AND,
        PHPToken::T_BOOLEAN_OR,
        PHPToken::T_CONSTANT_ENCAPSED_STRING,
        PHPToken::T_DNUMBER,
        PHPToken::T_IS_EQUAL,
        PHPToken::T_IS_GREATER_OR_EQUAL,
        PHPToken::T_IS_IDENTICAL,
        PHPToken::T_IS_NOT_EQUAL,
        PHPToken::T_IS_NOT_IDENTICAL,
        PHPToken::T_IS_SMALLER_OR_EQUAL,
        PHPToken::T_LNUMBER,
        PHPToken::T_LOGICAL_AND,
        PHPToken::T_LOGICAL_OR,
        PHPToken::T_WHITESPACE,
        PHPToken::T_ANGLE_BRACKET_OPEN,
        PHPToken::T_ANGLE_BRACKET_CLOSE,
        PHPToken::T_PARENTHESES_OPEN,
        PHPToken::T_PARENTHESES_CLOSE,
        PHPToken::T_PLUS,
        PHPToken::T_MINUS,
        PHPToken::T_DIV,
        PHPToken::T_MOD,
        PHPToken::T_MUL,
        PHPToken::T_CONCAT,
        PHPToken::T_COMMA,
        PHPToken::T_TRUE,
        PHPToken::T_FALSE,
        PHPToken::T_NULL,
    ];

    /**
     * @inheritDoc
     */
    public function __construct(string $string, array $values = [])
    {
        $this->nodes = new Nodes();
        $statements = 0;
        $counter = new Counter();
        $openConditions = new Counter();
        $conditions = new Condition('boolean');
        $tokenizer = new Tokenizer($string);

        while (($token = $tokenizer->next()) !== ITokenizer::T_EOF) {
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_OPEN_STATEMENT) {
                $statements++;

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_CLOSE_STATEMENT) {
                $statements--;

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_VARIABLE && $conditions->isSatisfies()) {
                /** @psalm-suppress PossiblyInvalidArgument */
                $this->variable($counter, $tokenizer, $token, $values);

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if (
                ($token->getType() === Token::T_IF)
                || ($token->getType() === Token::T_ELSEIF && !$conditions->isSatisfies())
            ) {
                /** @psalm-suppress PossiblyInvalidArgument */
                $this->condition($conditions, $openConditions, $tokenizer, $token, $values);

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_ELSE && !$conditions->isSatisfies()) {
                if (!$conditions->isEmpty()) {
                    $conditions->set($conditions->count() - 1, !$conditions->get($conditions->count() - 1));
                }

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_ELSE && $conditions->isSatisfies()) {
                $conditions->set($conditions->count() - 1, false);
            }

            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_ENDIF) {
                $conditions->delete($conditions->count() - 1);
                $conditions->resetKeys();
                $openConditions->decrement();

                continue;
            }
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if ($token->getType() === Token::T_TEXT && $conditions->isSatisfies()) {
                $image = str_replace(
                    ['\\\\', '\\{{', '\\}}'],
                    ['\\', '{{', '}}',],
                    $token->getImage()
                );
                $this->nodes[] = new Text($image);
            }
        }
        if ($statements > 0) {
            throw new FormatErrorException('Format string has error in closed statements');
        }
        if ($statements < 0) {
            throw new FormatErrorException('Format string has error in opened statements');
        }
        if ($openConditions->get() !== 0) {
            throw new FormatErrorException('Format string has error in conditions');
        }
    }

    /**
     * @inheritDoc
     */
    public function getNodes(): INodes
    {
        return $this->nodes;
    }

    /**
     * Переменная
     *
     * @param mixed[] $values
     */
    private function variable(Counter $counter, Tokenizer $tokenizer, IToken $tokenVariable, array $values): void
    {
        $path = $tokenVariable->getImage();
        if ($path === '') {
            $path = (string) $counter->get();
            $counter->increment();
        }
        if ($tokenizer->lookAtNextType() === Token::T_WHITESPACE) {
            $tokenizer->next();
        }
        $specifier = null;
        if ($tokenizer->lookAtNextType() === Token::T_SEPARATOR) {
            /** @psalm-suppress PossiblyInvalidArgument */
            $specifier = $this->specifier($tokenizer, $tokenizer->next(), $values);
        }

        $this->nodes[] = new Variable($path, $values, $specifier);
    }

    /**
     * Спецификатор
     *
     * @param mixed[] $values
     */
    private function specifier(Tokenizer $tokenizer, IToken $tokenSeparator, array $values): ISpecifier
    {
        $tokenSpecifier = $tokenizer->next();
        if ($tokenSpecifier === ITokenizer::T_EOF) {
            throw new FormatErrorException(
                sprintf(
                    'Specifier not set (%d:%d)',
                    $tokenSeparator->getEndLine(),
                    $tokenSeparator->getEndColumn()
                )
            );
        }
        /** @psalm-suppress PossiblyInvalidMethodCall */
        if ($tokenSpecifier->getType() === Token::T_WHITESPACE) {
            $tokenSpecifier = $tokenizer->next();
        }
        /** @psalm-suppress PossiblyInvalidMethodCall */
        if ($tokenSpecifier === ITokenizer::T_EOF || $tokenSpecifier->getType() !== Token::T_SPECIFIER) {
            throw new FormatErrorException(
                sprintf(
                    'Specifier not set (%d:%d)',
                    $tokenSeparator->getEndLine(),
                    $tokenSeparator->getEndColumn()
                )
            );
        }

        $modifiers = [];
        if ($tokenizer->lookAtNextType() === Token::T_OPEN_PARENTHESES) {
            $tokenizer->next();
            do {
                $isSingle = false;
                $isQuote = false;
                $token = $tokenizer->next();
                if ($token === ITokenizer::T_EOF) {
                    throw new FormatErrorException(
                        sprintf(
                            'Specifier not set (%d:%d)',
                            $tokenSeparator->getEndLine(),
                            $tokenSeparator->getEndColumn()
                        )
                    );
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_WHITESPACE) {
                    $token = $tokenizer->next();
                    if ($token === ITokenizer::T_EOF) {
                        throw new FormatErrorException(
                            sprintf(
                                'Specifier not set (%d:%d)',
                                $tokenSeparator->getEndLine(),
                                $tokenSeparator->getEndColumn()
                            )
                        );
                    }
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_CLOSE_PARENTHESES && !count($modifiers)) {
                    break;
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_QUOTE) {
                    $isQuote = $token->getImage() === '"';
                    $isSingle = $token->getImage() === "'";
                    $token = $tokenizer->next();
                    if ($token === ITokenizer::T_EOF) {
                        throw new FormatErrorException(
                            sprintf(
                                'Specifier not set (%d:%d)',
                                $tokenSeparator->getEndLine(),
                                $tokenSeparator->getEndColumn()
                            )
                        );
                    }
                }

                /** @psalm-suppress PossiblyInvalidMethodCall */
                if (
                    !in_array(
                        $token->getType(),
                        [Token::T_MODIFIER, Token::T_TRUE, Token::T_FALSE, Token::T_NULL, Token::T_QUOTE,]
                    )
                    && $token->getType() !== Token::T_CLOSE_PARENTHESES
                ) {
                    throw new FormatErrorException(
                        sprintf(
                            'Specifier syntax error (%d:%d)',
                            $tokenSeparator->getEndLine(),
                            $tokenSeparator->getEndColumn()
                        )
                    );
                }
                $isVariable = true;
                /** @psalm-suppress PossiblyInvalidMethodCall */
                $value = $token->getImage();
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_QUOTE) {
                    $value = '';
                }
                /** @psalm-suppress PossiblyInvalidArgument */
                $this->castValue($token, $value, $isVariable, $isQuote, $isSingle);
                $modifiers[] = new Modifier($value, $values, $isVariable);

                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() !== Token::T_QUOTE) {
                    $token = $tokenizer->next();
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if (
                    $token === ITokenizer::T_EOF
                    || ($isQuote && $token->getImage() !== '"')
                    || ($isSingle && $token->getImage() !== '\'')
                ) {
                    throw new FormatErrorException(
                        sprintf(
                            'Quote syntax error (%d:%d)',
                            $tokenSeparator->getEndLine(),
                            $tokenSeparator->getEndColumn()
                        )
                    );
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_QUOTE) {
                    $token = $tokenizer->next();
                    if ($token === ITokenizer::T_EOF) {
                        throw new FormatErrorException(
                            sprintf(
                                'Specifier not set (%d:%d)',
                                $tokenSeparator->getEndLine(),
                                $tokenSeparator->getEndColumn()
                            )
                        );
                    }
                }
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($token->getType() === Token::T_WHITESPACE) {
                    $token = $tokenizer->next();
                    if ($token === ITokenizer::T_EOF) {
                        throw new FormatErrorException(
                            sprintf(
                                'Specifier not set (%d:%d)',
                                $tokenSeparator->getEndLine(),
                                $tokenSeparator->getEndColumn()
                            )
                        );
                    }
                }

                /** @psalm-suppress PossiblyInvalidMethodCall */
                $loop = $token !== ITokenizer::T_EOF && $token->getType() !== Token::T_CLOSE_PARENTHESES;

                /** @psalm-suppress PossiblyInvalidMethodCall */
                if ($loop && $token->getType() !== Token::T_COMMA_SEPARATOR) {
                    throw new FormatErrorException(
                        sprintf(
                            'Comma separator syntax error (%d:%d - %d:%d)',
                            $tokenSeparator->getStartLine(),
                            $tokenSeparator->getStartColumn(),
                            $tokenSeparator->getEndLine(),
                            $tokenSeparator->getEndColumn()
                        )
                    );
                }
            } while ($loop);
        }

        /** @psalm-suppress PossiblyInvalidMethodCall */
        return new Specifier($tokenSpecifier->getImage(), $modifiers);
    }

    /**
     * Условие
     *
     * @param mixed[] $values
     */
    private function condition(
        Condition $conditions,
        Counter $openConditions,
        Tokenizer $tokenizer,
        IToken $tokenIf,
        array $values
    ): void {
        $token = $tokenizer->next();
        if ($token === ITokenizer::T_EOF) {
            throw new FormatErrorException(
                sprintf(
                    'Syntax error in condition (%d:%d)',
                    $tokenIf->getEndLine(),
                    $tokenIf->getEndColumn()
                )
            );
        }
        /** @psalm-suppress PossiblyInvalidMethodCall */
        if ($token->getType() === Token::T_WHITESPACE) {
            $token = $tokenizer->next();
            if ($token === ITokenizer::T_EOF) {
                throw new FormatErrorException(
                    sprintf(
                        'Syntax error in condition (%d:%d)',
                        $tokenIf->getEndLine(),
                        $tokenIf->getEndColumn()
                    )
                );
            }
        }
        /** @psalm-suppress PossiblyInvalidMethodCall */
        if ($token->getType() !== Token::T_OPEN_PARENTHESES) {
            throw new FormatErrorException(
                sprintf(
                    'No open parentheses after "if" keyword (%d:%d)',
                    $tokenIf->getEndLine(),
                    $tokenIf->getEndColumn()
                )
            );
        }
        $ifCondition = '';
        do {
            $token = $tokenizer->next();

            /** @psalm-suppress PossiblyInvalidMethodCall */
            $loop = $token !== ITokenizer::T_EOF && $token->getType() !== Token::T_CLOSE_PARENTHESES;

            if ($loop) {
                /** @psalm-suppress PossiblyInvalidMethodCall */
                $value = $token->getImage();
                /** @psalm-suppress PossiblyInvalidMethodCall */
                if (
                    in_array(
                        $token->getType(),
                        [Token::T_CONDITION_PART, Token::T_TRUE, Token::T_FALSE, Token::T_NULL]
                    )
                ) {
                    $isQuote = $tokenizer->lookAtPrevType() === Token::T_QUOTE
                        && $tokenizer->lookAtPrevImage() === '"';
                    $isSingle = $tokenizer->lookAtPrevType() === Token::T_QUOTE
                        && $tokenizer->lookAtPrevImage() === "'";
                    $isVariable = true;
                    /** @psalm-suppress PossiblyInvalidArgument */
                    $this->castValue($token, $value, $isVariable, $isQuote, $isSingle);
                    $conditionPart = new ConditionPart($value, $values, $isVariable);
                    try {
                        $value = var_export($conditionPart->getValue(), true);
                    } catch (NotFoundKey $exception) {
                        $value = 'false';
                    }
                }
                $ifCondition .= $value;
            }
        } while ($loop);
        /** @psalm-suppress PossiblyInvalidMethodCall */
        if ($token === ITokenizer::T_EOF || $token->getType() !== Token::T_CLOSE_PARENTHESES) {
            throw new FormatErrorException(
                sprintf(
                    'No close parentheses in statement (%d:%d)',
                    $tokenIf->getEndLine(),
                    $tokenIf->getEndColumn()
                )
            );
        }
        $ifCondition = trim($ifCondition);
        if ($ifCondition === '') {
            throw new FormatErrorException(
                sprintf(
                    'Empty condition (%d:%d)',
                    $tokenIf->getEndLine(),
                    $tokenIf->getEndColumn()
                )
            );
        }

        $expressionResult = $this->expression($ifCondition);
        if ($tokenIf->getType() === Token::T_IF) {
            $conditions->add($expressionResult);
            $openConditions->increment();

            return;
        }
        if (!$conditions->isEmpty()) {
            $conditions->set($conditions->count() - 1, $expressionResult);
        }
    }

    /**
     * Значение
     *
     * @param mixed $value
     */
    private function castValue(IToken $token, &$value, bool &$isVariable, bool $isQuote, bool $isSingle): void
    {
        if ($token->getType() === Token::T_TRUE) {
            $value = true;
            $isVariable = false;
        }
        if ($token->getType() === Token::T_FALSE) {
            $value = false;
            $isVariable = false;
        }
        if ($token->getType() === Token::T_NULL) {
            $value = null;
            $isVariable = false;
        }
        if ($isQuote && is_string($value)) {
            $value = str_replace('\"', '"', $value);
            $isVariable = false;
        }
        if ($isSingle && is_string($value)) {
            $value = str_replace("\'", "'", $value);
            $isVariable = false;
        }
    }

    /**
     * Проверяет условие
     */
    private function expression(string $expression): bool
    {
        $tokenizer = TokenizerFactory::factory('<?php ' . $expression);
        // пропускаем открывающийся тег php
        $tokenizer->peekNextType();
        while (($token = $tokenizer->next()) !== ITokenizer::T_EOF) {
            /** @psalm-suppress PossiblyInvalidMethodCall */
            if (in_array($token->getType(), self::$allowPhp)) {
                continue;
            }

            /** @psalm-suppress PossiblyInvalidMethodCall */
            throw new FormatErrorException(
                sprintf(
                    'Not allowed statement "%s":"%s"',
                    $token->getImage(),
                    $token->getType()
                )
            );
        }
        $result = false;
        eval('$result = (' . $expression . ');');

        return (bool) $result;
    }
}
