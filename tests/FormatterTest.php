<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format;

use Fi1a\Format\Formatter;
use Fi1a\Unit\Format\Fixtures\FormatClass;
use PHPUnit\Framework\TestCase;

use const LC_ALL;

class FormatterTest extends TestCase
{
    public $key = 'key_value';

    /**
     * Данные для метода Formatter::format
     *
     * @return mixed[]
     */
    public function dataProviderFormat(): array
    {
        return [
            [
                '{{fix:fix3:fix4:fix5}} {{not_exist}} {{fix:fix3}}{{fix}}',
                [
                    'fix' => new FormatClass(),
                ],
                '5 {{not_exist}} arrayFIX',
            ],
            [
                '{{fix:fix1}} {{fix:fix2|1.1f}}',
                [
                    'fix' => new FormatClass(),
                ],
                'string 20.0',
            ],
            [
                "{{array:key2|d}}, {{\n array:key1 \t |1.1f}}",
                [
                    'array' => [
                        'key1' => 10,
                        'key2' => 20,
                    ],
                ],
                '20, 10.0',
            ],
            [
                "{{key2|d}}, {{\n key1 \t |1.1f}}",
                [
                    'key1' => 10,
                    'key2' => 20,
                ],
                '20, 10.0',
            ],
            [
                "{{key4|d}}, {{\n key2 \t |1.1f}}",
                [
                    'key1' => null,
                    'key2' => 10,
                    'key3' => null,
                    'key4' => 20,
                ],
                '20, 10.0',
            ],
            [
                "{{key2|d}}, {{\n key1 \t |02d}}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 01',
            ],
            [
                "{{key2|d}}, {{\n key1 \t | \t 02d\n }}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 01',
            ],
            [
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 1',
            ],
            [
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            [
                "{{key2|}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            [
                "{{|s}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '{{|}}, 1.1111',
            ],
            [
                "{{0|'.9d}}",
                [
                    123,
                ],
                '......123',
            ],
            [
                "%b = '{{0|b}}'",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\'',
            ],
            [
                "%%b%%% = '{{0|b}}'",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\'',
            ],
            [
                "%%b%%% = '{{0|b}}' %d%%%d\$d%",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\' %d%%%d$d%',
            ],
            [
                "%b = '{{0|b}}' $0%d",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\' $0%d',
            ],
            [
                "%c = '{{0|c}}'",
                [
                    65,
                ],
                '%c = \'A\'',
            ],
            [
                "%e = '{{0|e}}'",
                [
                    43951789,
                ],
                '%e = \'4.395179e+7\'',
            ],
            [
                "%+d = '{{0|+d}}'",
                [
                    43951789,
                ],
                '%+d = \'+43951789\'',
            ],
            [
                "%+d = '{{0|+d}}'",
                [
                    -43951789,
                ],
                '%+d = \'-43951789\'',
            ],
            [
                '{{0|10s}}',
                [
                    'string',
                ],
                '    string',
            ],
            [
                '{{0|-10s}}',
                [
                    'string',
                ],
                'string    ',
            ],
            [
                '{{0|010s}}',
                [
                    'string',
                ],
                '0000string',
            ],
            [
                "{{0|'#10s}}",
                [
                    'string',
                ],
                '####string',
            ],
            [
                '{{0|10.10s}}',
                [
                    'string value',
                ],
                'string val',
            ],
            [
                '{{0|04d}}-{{1|02d}}-{{2|02d}}',
                [
                    2016,
                    2,
                    27,
                ],
                '2016-02-27',
            ],
            [
                '{{key}}',
                [
                    'key' => true,
                ],
                'true',
            ],
            [
                '{{key}}',
                [
                    'key' => null,
                ],
                'null',
            ],
            [
                '{{key}}',
                [
                    'key' => new static(),
                ],
                'Fi1a\Unit\Format\FormatterTest',
            ],
            [
                '{{key}}',
                [
                    'key' => 0,
                ],
                '0',
            ],
            [
                '{{not_exist}}',
                [],
                '{{not_exist}}',
            ],
            [
                '{{key:key:key}}',
                [
                    'key' => new static(),
                ],
                '{{key:key:key}}',
            ],
            [
                '{{}} {{}} {{}}',
                [
                    1, 2 ,3,
                ],
                '1 2 3',
            ],
            [
                '{{',
                [
                    1,
                ],
                '{{',
            ],
            [
                '{{}}',
                [
                    1,
                ],
                '1',
            ],
            [
                '{{}} {{key',
                [
                    1,
                ],
                '1 {{key',
            ],
            [
                '{{key1}} {{}} {{key2}}',
                [
                    1,
                    'key1' => 2,
                ],
                '2 1 {{key2}}',
            ],
        ];
    }

    /**
     * Форматирование и подстановка значений в строке
     *
     * @param mixed[]  $values
     *
     * @dataProvider dataProviderFormat
     */
    public function testFormat(string $string, array $values, string $equal)
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        $this->assertEquals($equal, Formatter::format($string, $values));
    }
}
