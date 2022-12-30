<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Преобразует специальные символы в HTML-сущности
 */
class EscapeTest extends TestCase
{
    /**
     * Исключение при более чем три модификатора
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|escape(2, "UTF-8", true, 1)}}', [1]);
    }

    /**
     * Преобразует специальные символы в HTML-сущности
     */
    public function testFormat(): void
    {
        $this->assertEquals('&amp;quot;test&amp;quot;', Formatter::format('{{|escape}}', ['"test"']));
    }
}
