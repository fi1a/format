<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Format\AST\AST;
use Fi1a\Format\AST\NodeInterface;
use Fi1a\Format\AST\TextInterface;
use Fi1a\Format\AST\VariableInterface;
use Fi1a\Format\Exception\SpecifierNotFoundException;
use Fi1a\Format\Specifier\SpecifierInterface;
use InvalidArgumentException;

use const PREG_OFFSET_CAPTURE;

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
     * Сокращения
     *
     * @var string[]
     */
    private static $shortcuts = [];

    /**
     * @inheritDoc
     */
    public static function format(string $string, array $values = []): string
    {
        $matches = [];
        while (
            preg_match(
                '#(\{\{[\s\t\n]*)([0-9\w\_\:]*)([\s\t\n]*\|[\s\t\n]*|)~([^\}\s\t\n]*)([\s\t\n]*\}\})#',
                $string,
                $matches,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $shortcutName = mb_strtolower($matches[4][0]);
            if (!isset(static::$shortcuts[$shortcutName])) {
                throw new InvalidArgumentException(
                    sprintf('Shortcut "%s" not found', $matches[4][0])
                );
            }
            $string = substr($string, 0, $matches[4][1] - 1)
                . static::$shortcuts[$shortcutName]
                . substr($string, $matches[4][1] + mb_strlen($shortcutName));
        }

        $ast = new AST($string, $values);
        $formatted = '';

        /**
         * @var NodeInterface $node
         */
        foreach ($ast->getNodes() as $node) {
            if ($node instanceof VariableInterface) {
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
            if ($node instanceof TextInterface) {
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
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }
        if (static::hasSpecifier($name)) {
            return false;
        }
        if (!is_subclass_of($specifier, SpecifierInterface::class)) {
            throw new InvalidArgumentException('The class must implement the interface ' . SpecifierInterface::class);
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
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }

        return array_key_exists(mb_strtolower($name), static::$specifiers);
    }

    /**
     * @inheritDoc
     */
    public static function deleteSpecifier(string $name): bool
    {
        if (!$name) {
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
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
    public static function getSpecifier(string $name): SpecifierInterface
    {
        if (!$name) {
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }
        if (!static::hasSpecifier($name)) {
            throw new SpecifierNotFoundException(
                sprintf('Specifier "%s" not exists', $name)
            );
        }
        $class = static::$specifiers[mb_strtolower($name)];
        /**
         * @var SpecifierInterface $instance
         */
        $instance = new $class();

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public static function addShortcut(string $name, string $specifier): bool
    {
        if (!$name) {
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }
        if (!$specifier) {
            throw new InvalidArgumentException('Argument "$shortcut" cannot be empty');
        }
        if (static::hasShortcut($name)) {
            return false;
        }

        static::$shortcuts[mb_strtolower($name)] = $specifier;

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function hasShortcut(string $name): bool
    {
        if (!$name) {
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }

        return array_key_exists(mb_strtolower($name), static::$shortcuts);
    }

    /**
     * @inheritDoc
     */
    public static function deleteShortcut(string $name): bool
    {
        if (!$name) {
            throw new InvalidArgumentException('Argument "$name" cannot be empty');
        }

        if (!static::hasShortcut($name)) {
            return false;
        }
        unset(static::$shortcuts[mb_strtolower($name)]);

        return true;
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
