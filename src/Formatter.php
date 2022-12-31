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
class Formatter implements FormatterInterface
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
    public static function format(string $string, array $values = [], array $modifierValues = []): string
    {
        $matches = [];
        while (
            preg_match(
                '#(([^\\\]+|^)\{\{(.*))([\s\t\n]*\|[\s\t\n]*|([^\\\]*\{\{(.*)))'
                . '~([^\\\}\|\s\t\n]*)([\s\t\n]*([^\\\]*\}\})|(\|.+\}\}))#',
                $string,
                $matches,
                PREG_OFFSET_CAPTURE
            ) > 0
        ) {
            $shortcutName = mb_strtolower($matches[7][0]);
            if (!isset(static::$shortcuts[$shortcutName])) {
                throw new InvalidArgumentException(
                    sprintf('Shortcut "%s" not found', $matches[7][0])
                );
            }
            $string = substr($string, 0, $matches[7][1] - 1)
                . static::$shortcuts[$shortcutName]
                . substr($string, $matches[7][1] + mb_strlen($shortcutName));
        }

        $ast = new AST($string, $values, $modifierValues);
        $formatted = '';

        /**
         * @var NodeInterface $node
         */
        foreach ($ast->getNodes() as $node) {
            if ($node instanceof VariableInterface) {
                $formatted .= $node->getValue();

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
}
