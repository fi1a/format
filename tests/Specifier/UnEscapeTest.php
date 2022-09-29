<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Преобразует специальные HTML-сущности обратно в соответствующие символы
 */
class UnEscapeTest extends TestCase
{
    /**
     * Исключение при более чем одном модификаторе
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|unescape(2, true)}}', [1]);
    }

    /**
     * Преобразует специальные HTML-сущности обратно в соответствующие символы
     */
    public function testFormat(): void
    {
        $this->assertEquals('"test"', Formatter::format('{{|unescape}}', ['&quot;test&quot;']));
    }
}
