<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format;

use Fi1a\Format\Safe;
use PHPUnit\Framework\TestCase;

/**
 * Методы экранирования спец. символов
 */
class SafeTest extends TestCase
{
    /**
     * Экранирует все спец. символы
     */
    public function testEscape(): void
    {
        $this->assertEquals('\\{{|sprintf\\}} \\\\', Safe::escape('{{|sprintf}} \\'));
    }

    /**
     * Убирает экранирование спец. символов
     */
    public function testUnescape(): void
    {
        $this->assertEquals('{{|sprintf}} \\', Safe::unescape('\\{{|sprintf\\}} \\\\'));
    }
}
