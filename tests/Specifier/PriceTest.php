<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use const PHP_ROUND_HALF_DOWN;
use const PHP_ROUND_HALF_UP;

/**
 * Форматирование цены
 */
class PriceTest extends TestCase
{
    /**
     * Форматирование цены
     */
    public function testPrice(): void
    {
        $this->assertEquals(
            '100',
            Formatter::format('{{value|price}}', ['value' => 100.00])
        );
        $this->assertEquals(
            '100000000',
            Formatter::format('{{value|price}}', ['value' => 100000000.00])
        );
        $this->assertEquals(
            '100000000.67',
            Formatter::format('{{value|price}}', ['value' => 100000000.67])
        );
        $this->assertEquals(
            '100 100.00',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
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
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
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
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
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
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
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
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
                ['value' => 'string'],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                ]
            )
        );
        $this->assertEquals(
            '100 101',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision)}}',
                ['value' => 100100.5],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                    'round' => PHP_ROUND_HALF_UP,
                    'roundPrecision' => 0,
                ]
            )
        );
        $this->assertEquals(
            '100 100',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision)}}',
                ['value' => 100100.5],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                    'round' => PHP_ROUND_HALF_DOWN,
                    'roundPrecision' => 0,
                ]
            )
        );
        $this->assertEquals(
            '100 100.2',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision)}}',
                ['value' => 100100.15],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                    'round' => PHP_ROUND_HALF_UP,
                    'roundPrecision' => 1,
                ]
            )
        );
        $this->assertEquals(
            '100 100.00',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision)}}',
                ['value' => 100100.05],
                [
                    'decimals' => 2,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => true,
                    'round' => PHP_ROUND_HALF_DOWN,
                    'roundPrecision' => 1,
                ]
            )
        );
        $this->assertEquals(
            '100 100',
            Formatter::format(
                '{{value|price(decimals, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision, floor)}}',
                ['value' => 100100.6],
                [
                    'decimals' => 0,
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => false,
                    'round' => null,
                    'roundPrecision' => 0,
                    'floor' => true,
                ]
            )
        );
        $this->assertEquals(
            '100 100.00',
            Formatter::format(
                '{{value|price(2.0, decimalSeparator, thousandsSeparator, '
                . 'allowZeroDecimal, round, roundPrecision, floor)}}',
                ['value' => 100100.6],
                [
                    'decimalSeparator' => '.',
                    'thousandsSeparator' => ' ',
                    'allowZeroDecimal' => true,
                    'round' => null,
                    'roundPrecision' => 0,
                    'floor' => true,
                ]
            )
        );
    }

    /**
     * Форматирование цены
     */
    public function testPriceException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{value|price("1", "2", "3", "4", "5", "6", "7", "8")}}', ['value' => 100.00]);
    }
}
