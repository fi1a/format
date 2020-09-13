<?php

declare(strict_types=1);

namespace Fi1a\Format;

/**
 * Интерфейс класса форматирования строковых шаблонов
 */
interface IFormatter
{
    /**
     * Форматирование строковых шаблонов
     *
     * <code>
     * S::simpleFormat('{{key|b}}',[ 'key' => 43951789, ]); // string "10100111101010011010101101"
     * S::simpleFormat('{{key|c}}',[ 'key' => 65, ]); // string "A"
     * S::simpleFormat('{{key|e}}',[ 'key' => 43951789, ]); // string "4.395179e+7"
     *
     * S::simpleFormat('{{0|04d}}-{{1|02d}}-{{2|02d}}',[ 2016, 2, 27, ]); // string "2016-02-27"
     *
     * S::simpleFormat('{{key|10s}}',[ 'key' => 'string', ]); // string "    string"
     * S::simpleFormat('{{key|-10s}}',[ 'key' => 'string', ]); // string "string    "
     * </code>
     *
     * @param string $string строка для форматирования
     * @param mixed[]  $values значения для подстановки
     */
    public static function format(string $string, array $values = []): string;
}
