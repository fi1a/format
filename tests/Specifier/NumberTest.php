<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Форматирование числа
 */
class NumberTest extends TestCase
{
    /**
     * Форматирование числа
     */
    public function testNumber(): void
    {
        $this->assertEquals(
            '100',
            Formatter::format('{{value|number}}', ['value' => 100.00])
        );
        $this->assertEquals(
            '100000000',
            Formatter::format('{{value|number}}', ['value' => 100000000.00])
        );
        $this->assertEquals(
            '100000000.67',
            Formatter::format('{{value|number}}', ['value' => 100000000.67])
        );
        $this->assertEquals(
            '100 100.00',
            Formatter::format(
                '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 100100.00],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => true,
                ]
            )
        );
        $this->assertEquals(
            '100 100',
            Formatter::format(
                '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 100100.00],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                ]
            )
        );
        $this->assertEquals(
            '100 100.1',
            Formatter::format(
                '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 100100.10],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                ]
            )
        );
        $this->assertEquals(
            '100 100.12',
            Formatter::format(
                '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 100100.12],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                ]
            )
        );
        $this->assertEquals(
            '0',
            Formatter::format(
                '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 'string'],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                ]
            )
        );
    }

    /**
     * Форматирование числа
     */
    public function testPriceException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{value|number("1", "2", "3", "4", "5")}}', ['value' => 100.00]);
    }
}
