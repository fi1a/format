<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\Memory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Форматирование размера памяти
 */
class MemoryTest extends TestCase
{
    /**
     * Исключение при более чем одном модификаторе
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|memory("B", "MB")}}', [1024]);
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
                '{{|memory}}',
                [
                    1023,
                ],
                '1023.0 Б',
            ],
            // 1
            [
                '{{|memory("B")}}',
                [
                    1023,
                ],
                '1023.0 Б',
            ],
            // 2
            [
                '{{|memory}}',
                [
                    1024,
                ],
                '1.0 КБ',
            ],
            // 3
            [
                '{{|memory("B")}}',
                [
                    1024,
                ],
                '1024.0 Б',
            ],
            // 4
            [
                '{{|memory}}',
                [
                    1024 * 1024,
                ],
                '1.0 МБ',
            ],
            // 5
            [
                '{{|memory("B")}}',
                [
                    1024 * 1024,
                ],
                (1024 * 1024) . '.0 Б',
            ],
            // 6
            [
                '{{|memory}}',
                [
                    1024 * 1024 * 1024,
                ],
                '1.0 ГБ',
            ],
            // 7
            [
                '{{|memory("B")}}',
                [
                    1024 * 1024 * 1024,
                ],
                (1024 * 1024 * 1024) . '.0 Б',
            ],
            // 8
            [
                '{{|memory}}',
                [
                    1024 * 1024 * 1024 * 1024,
                ],
                '1.0 ТБ',
            ],
            // 9
            [
                '{{|memory("B")}}',
                [
                    1024 * 1024 * 1024 * 1024,
                ],
                (1024 * 1024 * 1024 * 1024) . '.0 Б',
            ],
            // 10
            [
                '{{|memory}}',
                [
                    1024 * 1024 * 1024 * 1024 * 1024,
                ],
                '1.0 ПБ',
            ],
            // 11
            [
                '{{|memory("B")}}',
                [
                    1024 * 1024 * 1024 * 1024 * 1024,
                ],
                (1024 * 1024 * 1024 * 1024 * 1024) . '.0 Б',
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
    public function testSetDimensionException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Memory::setDimensions([]);
    }

    /**
     * Исключение при отсутвии ключей
     */
    public function testSetDimension(): void
    {
        Memory::setDimensions([
            'B' => 'B',
            'KB' => 'KB',
            'MB' => 'MB',
            'GB' => 'GB',
            'TB' => 'TB',
            'PB' => 'PB',
        ]);
        $this->assertEquals('1.0 KB', Formatter::format('{{|memory}}', [1024]));
        Memory::setDimensions([
            'B' => 'Б',
            'KB' => 'КБ',
            'MB' => 'МБ',
            'GB' => 'ГБ',
            'TB' => 'ТБ',
            'PB' => 'ПБ',
        ]);
    }
}
