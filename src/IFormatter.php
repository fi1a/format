<?php

declare(strict_types=1);

namespace Fi1a\Format;

use Fi1a\Format\Specifier\ISpecifier;

/**
 * Интерфейс класса форматирования строковых шаблонов
 */
interface IFormatter
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
    public static function getSpecifier(string $name): ISpecifier;
}
