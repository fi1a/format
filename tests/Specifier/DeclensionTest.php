<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Склонение слов после числительных
 */
class DeclensionTest extends TestCase
{
    /**
     * Склонение слов после числительных
     */
    public function testDeclension(): void
    {
        $this->assertEquals(
            '1 год',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 1])
        );
        $this->assertEquals(
            '2 года',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 2])
        );
        $this->assertEquals(
            '3 года',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 3])
        );
        $this->assertEquals(
            '4 года',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 4])
        );
        $this->assertEquals(
            '5 лет',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 5])
        );
        $this->assertEquals(
            '100 лет',
            Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 100])
        );
        $this->assertEquals(
            '32345433343 года',
            Formatter::format(
                '{{year}} {{year|declension("год", "года", "лет")}}',
                ['year' => 32345433343]
            )
        );
    }

    /**
     * Исключение при двух модификаторах
     */
    public function testDeclensionException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{year|declension("год", "года")}}', ['year' => 1]);
    }

    /**
     * Исключение при больше чем три модификатора
     */
    public function testDeclensionExceptionMore(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{year|declension("год", "года", "годов", "годов")}}', ['year' => 1]);
    }
}
