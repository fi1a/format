<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\Time;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Форматирование времени
 */
class TimeTest extends TestCase
{
    /**
     * Исключение при более чем одном модификаторе
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|time("B", "MB")}}', [1024]);
    }

    /**
     * Форматирование и подстановка значений в строке
     *
     * @return mixed[]
     */
    public function dataProviderFormat(): array
    {
        return [
            // 0
            [
                '{{|time}}',
                [
                    0,
                ],
                '< 1 сек.',
            ],
            // 1
            [
                '{{|time}}',
                [
                    1,
                ],
                '1 сек.',
            ],
            // 2
            [
                '{{|time}}',
                [
                    2,
                ],
                '2 сек.',
            ],
            // 3
            [
                '{{|time}}',
                [
                    59,
                ],
                '59 сек.',
            ],
            // 4
            [
                '{{|time}}',
                [
                    60,
                ],
                '1 мин.',
            ],
            // 5
            [
                '{{|time}}',
                [
                    120,
                ],
                '2 мин.',
            ],
            // 6
            [
                '{{|time}}',
                [
                    60 * 60,
                ],
                '1 ч.',
            ],
            // 7
            [
                '{{|time}}',
                [
                    2 * 60 * 60,
                ],
                '2 ч.',
            ],
            // 8
            [
                '{{|time}}',
                [
                    24 * 60 * 60,
                ],
                '1 д.',
            ],
            // 9
            [
                '{{|time}}',
                [
                    2 * 24 * 60 * 60,
                ],
                '2 д.',
            ],
            // 10
            [
                '{{|time}}',
                [
                    100 * 24 * 60 * 60,
                ],
                '100 д.',
            ],
        ];
    }

    /**
     * Форматирование и подстановка значений в строке
     *
     * @param mixed[] $values
     *
     * @dataProvider dataProviderFormat
     */
    public function testFormat(string $string, array $values, string $equal): void
    {
        $this->assertEquals($equal, Formatter::format($string, $values));
    }

    /**
     * Исключение при отсутвии ключей
     */
    public function testSetTimeException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Time::setTimes([]);
    }

    /**
     * Исключение при отсутвии ключей
     */
    public function testTimeException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|time("unknown")}}', [100]);
    }

    /**
     * Исключение при отсутвии ключей
     */
    public function testTime(): void
    {
        $this->assertEquals('0 д.', Formatter::format('{{|time("days")}}', [1]));
        $this->assertEquals('1 д.', Formatter::format('{{|time("days")}}', [24 * 60 * 60]));
        $this->assertEquals('2 д.', Formatter::format('{{|time("days")}}', [2 * 24 * 60 * 60]));
        $this->assertEquals('2880 мин.', Formatter::format('{{|time("minutes")}}', [2 * 24 * 60 * 60]));
    }

    /**
     * Единицы измерения времени
     */
    public function testSetTimes(): void
    {
        Time::setTimes([
            'less_sec' => '< 1 секунды',
            'sec' => '1 секунда',
            'secs' => 'секунд',
            'min' => '1 минута',
            'mins' => 'минут',
            'hr' => '1 час',
            'hrs' => 'часов',
            'day' => '1 день',
            'days' => 'дней',
        ]);
        $this->assertEquals('< 1 секунды', Formatter::format('{{|time}}', [0]));
        Time::setTimes([
            'less_sec' => '< 1 сек.',
            'sec' => '1 сек.',
            'secs' => 'сек.',
            'min' => '1 мин.',
            'mins' => 'мин.',
            'hr' => '1 ч.',
            'hrs' => 'ч.',
            'day' => '1 д.',
            'days' => 'д.',
        ]);
    }
}
