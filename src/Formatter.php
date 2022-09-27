<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Format\AST\AST;
use Fi1a\Format\AST\INode;
use Fi1a\Format\AST\IText;
use Fi1a\Format\AST\IVariable;
use Fi1a\Format\Exception\SpecifierNotFoundException;

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
        $specifiers = [
            'sprintf',
        ];
        $ast = new AST($string, $values);
        $formatted = '';

        /**
         * @var INode $node
         */
        foreach ($ast->getNodes() as $node) {
            if ($node instanceof IVariable) {
                $value = static::convert($node->getValue());
                $specifier = $node->getSpecifier();
                if ($specifier) {
                    if (!in_array($specifier->getName(), $specifiers)) {
                        throw new SpecifierNotFoundException(
                            sprintf('Specifier "%s" not exists', $specifier->getName())
                        );
                    }
                    /**
                     * @var mixed[] $args
                     */
                    $args = [$value];
                    foreach ($specifier->getModifiers() as $modifier) {
                        /** @psalm-suppress MixedAssignment */
                        $args[] = $modifier->getValue();
                    }
                    $formatted .= (string) call_user_func_array(
                        [static::class, $specifier->getName()],
                        $args
                    );

                    continue;
                }

                $formatted .= $value;

                continue;
            }
            if ($node instanceof IText) {
                $formatted .= $node->getText();
            }
        }

        return $formatted;
    }

    /**
     * Спецификатор sprintf
     *
     * @param mixed ...$modifiers
     */
    private function sprintf(string $value, ...$modifiers): string
    {
        $modifier = (string) reset($modifiers);
        if (!$modifier) {
            $modifier = 's';
        }
        $modifier = '%1$' . $modifier;

        return sprintf($modifier, $value);
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
