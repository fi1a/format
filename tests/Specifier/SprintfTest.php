<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Спецификатор функции sprintf
 */
class SprintfTest extends TestCase
{
    /**
     * Исключение при более чем одном модификаторе
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|sprintf("1", "2")}}', [1]);
    }
}
