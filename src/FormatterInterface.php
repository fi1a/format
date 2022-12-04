<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Format\Specifier\SpecifierInterface;

/**
 * Интерфейс класса форматирования строковых шаблонов
 */
interface FormatterInterface
{
    /**
     * Форматирование строковых шаблонов
     *
     * <code>
     * Formatter::format('{{key|sprintf("b")}}',[ 'key' => 43951789, ]); // string "10100111101010011010101101"
     * Formatter::format('{{key|sprintf("c")}}',[ 'key' => 65, ]); // string "A"
     * Formatter::format('{{key|sprintf("e")}}',[ 'key' => 43951789, ]); // string "4.395179e+7"
     *
     * Formatter::format('{{key|sprintf("10s")}}',[ 'key' => 'string', ]); // string "    string"
     * Formatter::format('{{key|sprintf("-10s")}}',[ 'key' => 'string', ]); // string "string    "
     * </code>
     *
     * @param string $string строка для форматирования
     * @param mixed[]  $values значения для подстановки
     *
     * @codeCoverageIgnore
     */
    public static function format(string $string, array $values = []): string;

    /**
     * Добавить функцию спецификаторов
     */
    public static function addSpecifier(string $name, string $specifier): bool;

    /**
     * Есть ли функция спецификаторов с таким именем
     */
    public static function hasSpecifier(string $name): bool;

    /**
     * Удалить функцию спецификаторов с таким именем
     */
    public static function deleteSpecifier(string $name): bool;

    /**
     * Возвращает функцию спецификатор с таким именем
     */
    public static function getSpecifier(string $name): SpecifierInterface;

    /**
     * Добавить сокращение
     */
    public static function addShortcut(string $name, string $specifier): bool;

    /**
     * Есть ли сокращение
     */
    public static function hasShortcut(string $name): bool;

    /**
     * Удалить сокращение
     */
    public static function deleteShortcut(string $name): bool;
}
