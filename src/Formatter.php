<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Format\AST\AST;
use Fi1a\Format\AST\INode;
use Fi1a\Format\AST\IText;
use Fi1a\Format\AST\IVariable;
use Fi1a\Format\Exception\SpecifierNotFoundException;
use Fi1a\Format\Specifier\ISpecifier;

/**
 * Форматирование строковых шаблонов
 */
class Formatter implements IFormatter
{
    /**
     * @var string[]
     */
    private static $specifiers = [];

    /**
     * @inheritDoc
     */
    public static function format(string $string, array $values = []): string
    {
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
                    $specifierInstance = static::getSpecifier($specifier->getName());
                    /**
                     * @var mixed[] $args
                     */
                    $args = [$value];
                    foreach ($specifier->getModifiers() as $modifier) {
                        /** @psalm-suppress MixedAssignment */
                        $args[] = $modifier->getValue();
                    }
                    $formatted .= (string) call_user_func_array(
                        [$specifierInstance, 'format'],
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
     * @inheritDoc
     */
    public static function addSpecifier(string $name, string $specifier): bool
    {
        if (!$name) {
            throw new \InvalidArgumentException('Parameter "$name" cannot be empty');
        }
        if (static::hasSpecifier($name)) {
            return false;
        }
        if (!is_subclass_of($specifier, ISpecifier::class)) {
            throw new \InvalidArgumentException('The class must implement the interface ' . ISpecifier::class);
        }
        static::$specifiers[mb_strtolower($name)] = $specifier;

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function hasSpecifier(string $name): bool
    {
        if (!$name) {
            throw new \InvalidArgumentException('Parameter "$name" cannot be empty');
        }

        return array_key_exists(mb_strtolower($name), static::$specifiers);
    }

    /**
     * @inheritDoc
     */
    public static function deleteSpecifier(string $name): bool
    {
        if (!$name) {
            throw new \InvalidArgumentException('Parameter "$name" cannot be empty');
        }
        if (!static::hasSpecifier($name)) {
            return false;
        }
        unset(static::$specifiers[mb_strtolower($name)]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getSpecifier(string $name): ISpecifier
    {
        if (!$name) {
            throw new \InvalidArgumentException('Parameter "$name" cannot be empty');
        }
        if (!static::hasSpecifier($name)) {
            throw new SpecifierNotFoundException(
                sprintf('Specifier "%s" not exists', $name)
            );
        }
        $class = static::$specifiers[mb_strtolower($name)];
        /**
         * @var ISpecifier $instance
         */
        $instance = new $class();

        return $instance;
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
