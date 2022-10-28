<?php

declare(strict_types=1);

namespace Fi1a\Format;

/**
 * Методы экранирования спец. символов
 */
class Safe
{
    /**
     * Экранирует все спец. символы
     */
    public static function escape(string $message): string
    {
        return str_replace(['\\', '{{', '}}'], ['\\\\', '\{{', '\}}'], $message);
    }

    /**
     * Убирает экранирование спец. символов
     */
    public static function unescape(string $message): string
    {
        return str_replace(
            ['\\\\', '\\{{', '\\}}'],
            ['\\', '{{', '}}',],
            $message
        );
    }
}
