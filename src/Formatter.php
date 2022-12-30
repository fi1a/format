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
     * @var mixed[][]
     */
    private static $always = [];

    /**
     * @inheritDoc
     */
    public static function format(string $string, array $values = [], array $modifierValues = []): string
    {
        if (count(static::getAlways())) {
            $string = static::applyAlwaysSpecifier($string, static::getAlways());
        }

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
                $value = static::convert($node->getValue());

                if (count($node->getSpecifiers())) {
                    foreach ($node->getSpecifiers() as $specifier) {
                        $specifierInstance = static::getSpecifier($specifier->getName());
                        /**
                         * @var mixed[] $args
                         */
                        $args = [$value];
                        foreach ($specifier->getModifiers() as $modifier) {
                            /** @psalm-suppress MixedAssignment */
                            $args[] = $modifier->getValue();
                        }
                        /** @var string $value */
                        $value = call_user_func_array(
                            [$specifierInstance, 'format'],
                            $args
                        );
                    }
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
     * @inheritDoc
     */
    public static function useAlways(string $specifier, int $sort = 500): bool
    {
        if (static::hasAlways($specifier)) {
            return false;
        }

        static::$always[$specifier] = [
            'sort' => $sort,
        ];

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function hasAlways(string $specifier): bool
    {
        return array_key_exists($specifier, static::$always);
    }

    /**
     * @inheritDoc
     */
    public static function unuseAlways(string $specifier): bool
    {
        if (!static::hasAlways($specifier)) {
            return false;
        }
        unset(static::$always[$specifier]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getAlways(): array
    {
        uasort(static::$always, function (array $a, array $b) {
            return (int) $a['sort'] - (int) $b['sort'];
        });
        /** @var string[] $always */
        $always = array_keys(static::$always);

        return $always;
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

    /**
     * Добавляет спецификаторы, которые всегда используются
     *
     * @param string[] $always
     */
    private static function applyAlwaysSpecifier(string $string, array $always): string
    {
        $alwaysPart = implode('|', $always);
        $matches = [];
        $offset = 0;
        while (
            preg_match(
                '#(\{\{)([\s\t\n]*)([\s\t\n]*)([^}\|]*)([\s\t\n]*)(\}\}|\|(.+)\}\})#ui',
                $string,
                $matches,
                PREG_OFFSET_CAPTURE,
                $offset
            ) > 0
        ) {
            $key = 4;
            $offset = $matches[$key][1] + mb_strlen('|' . $alwaysPart);

            if (
                preg_match('#^if([\s\t\n]*\(|)#i', $matches[$key][0]) > 0
                || preg_match('#^elseif([\s\t\n]*\(|)#i', $matches[$key][0]) > 0
                || preg_match('#^([\s\t\n]*)endif([\s\t\n]*)$#i', $matches[$key][0]) > 0
                || preg_match('#^([\s\t\n]*)else([\s\t\n]*)$#i', $matches[$key][0]) > 0
            ) {
                continue;
            }

            $string = substr($string, 0, $matches[$key][1])
                . $matches[$key][0]
                . '|' . $alwaysPart
                . substr($string, $matches[$key][1] + mb_strlen($matches[$key][0]));
        }

        return $string;
    }
}
