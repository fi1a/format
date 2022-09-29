<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format;

use Fi1a\Format\AST\Exception\FormatErrorException;
use Fi1a\Format\Exception\SpecifierNotFoundException;
use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\ISpecifier;
use Fi1a\Unit\Format\Fixtures\FormatClass;
use Fi1a\Unit\Format\Fixtures\Specifier;
use InvalidArgumentException;
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
                '{{}}',
                ['value'],
                'value',
            ],
            [
                '{{   }}',
                ['value'],
                'value',
            ],
            [
                '{{}} {{}}',
                ['value1', 'value2'],
                'value1 value2',
            ],
            [
                '{{}} {{0}} {{}}',
                ['value1', 'value2'],
                'value1 value1 value2',
            ],
            [
                '{{key1:key2}} {{key1:key3}}',
                [
                    'key1' => [
                        'key2' => 'value2',
                        'key3' => 'value3',
                    ],
                ],
                'value2 value3',
            ],
            [
                '{{  key1:key2  }} {{  key1:key3  }}',
                [
                    'key1' => [
                        'key2' => 'value2',
                        'key3' => 'value3',
                    ],
                ],
                'value2 value3',
            ],
            [
                '{{fix:fix3:fix4:fix5}} {{fix:fix3}}{{fix}}',
                [
                    'fix' => new FormatClass(),
                ],
                '5 arrayFIX',
            ],
            [
                'text {{|sprintf}} text',
                ['value'],
                'text value text',
            ],
            [
                'text {{  key1:key2  |  sprintf  }} text',
                [
                    'key1' => [
                        'key2' => 'value2',
                        'key3' => 'value3',
                    ],
                ],
                'text value2 text',
            ],
            [
                'text {{|sprintf()}} text',
                ['value'],
                'text value text',
            ],
            [
                '{{fix:fix1}} {{fix:fix2|sprintf("1.1f")}}',
                [
                    'fix' => new FormatClass(),
                ],
                'string 20.0',
            ],
            [
                "{{array:key2|sprintf('d')}}, {{\n array:key1 \t |sprintf('1.1f')}}",
                [
                    'array' => [
                        'key1' => 10,
                        'key2' => 20,
                    ],
                ],
                '20, 10.0',
            ],
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t |sprintf('1.1f')}}",
                [
                    'key1' => 10,
                    'key2' => 20,
                ],
                '20, 10.0',
            ],
            [
                "{{key4|sprintf('d')}}, {{\n key2 \t |sprintf('1.1f')}}",
                [
                    'key1' => null,
                    'key2' => 10,
                    'key3' => null,
                    'key4' => 20,
                ],
                '20, 10.0',
            ],
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t |sprintf('02d')}}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 01',
            ],
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t | \t sprintf('02d')\n }}",
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
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            [
                "{{|sprintf('s')}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    0 => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            [
                "{{0|sprintf('\'.9d')}}",
                [
                    123,
                ],
                '......123',
            ],
            [
                "%b = '{{0|sprintf('b')}}'",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\'',
            ],
            [
                "%%b%%% = '{{0|sprintf('b')}}'",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\'',
            ],
            [
                "%%b%%% = '{{0|sprintf('b')}}' %d%%%d\$d%",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\' %d%%%d$d%',
            ],
            [
                "%b = '{{0|sprintf('b')}}' $0%d",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\' $0%d',
            ],
            [
                "%c = '{{0|sprintf('c')}}'",
                [
                    65,
                ],
                '%c = \'A\'',
            ],
            [
                "%e = '{{0|sprintf('e')}}'",
                [
                    43951789,
                ],
                '%e = \'4.395179e+7\'',
            ],
            [
                "%+d = '{{0|sprintf('+d')}}'",
                [
                    43951789,
                ],
                '%+d = \'+43951789\'',
            ],
            [
                "%+d = '{{0|sprintf('+d')}}'",
                [
                    -43951789,
                ],
                '%+d = \'-43951789\'',
            ],
            [
                '{{0|sprintf("10s")}}',
                [
                    'string',
                ],
                '    string',
            ],
            [
                '{{0|sprintf("-10s")}}',
                [
                    'string',
                ],
                'string    ',
            ],
            [
                '{{0|sprintf(\'010s\')}}',
                [
                    'string',
                ],
                '0000string',
            ],
            [
                "{{0|sprintf('\'#10s')}}",
                [
                    'string',
                ],
                '####string',
            ],
            [
                '{{0|sprintf(\'10.10s\')}}',
                [
                    'string value',
                ],
                'string val',
            ],
            [
                '{{0|sprintf(\'04d\')}}-{{1|sprintf(\'02d\')}}-{{2|sprintf(\'02d\')}}',
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
                '{{}} {{}} {{}}',
                [
                    1, 2 ,3,
                ],
                '1 2 3',
            ],
            [
                '{{}}',
                [
                    1,
                ],
                '1',
            ],
            [
                '{{|spf(false, true, null)}}',
                [
                    1,
                ],
                '1',
            ],
            [
                '{{key1:key2|sprintf(modifier)}}',
                [
                    'key1' => [
                        'key2' => 123,
                    ],
                    'modifier' => '\'.9d',
                ],
                '......123',
            ],
            [
                '{{if ( key1:key2 ) }}{{ key1:key2 }}{{ endif }}',
                [
                    'key1' => [
                        'key2' => 2,
                    ],
                ],
                '2',
            ],
            [
                'text{{if(key1:key2)}}{{key1:key2}}{{endif}}value',
                [
                    'key1' => [
                        'key2' => false,
                    ],
                ],
                'textvalue',
            ],
            [
                'text{{if(key1:key2)}}{{key1:key2}}{{else}}{{key1:key3}}{{endif}}value',
                [
                    'key1' => [
                        'key2' => false,
                        'key3' => '_',
                    ],
                ],
                'text_value',
            ],
            [
                'text{{if(key1:key2)}}{{key1:key2}}{{elseif(key1:key3)}}{{key1:key3}}{{endif}}value',
                [
                    'key1' => [
                        'key2' => false,
                        'key3' => '_',
                    ],
                ],
                'text_value',
            ],
            [
                '{{if(key1:key3)}}{{key1:key2}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '2',
            ],
            [
                '{{if(true)}} {{key1:key2}} {{key1:key3}} {{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                ' 2 3 ',
            ],
            [
                '{{if(key1:key4)}}{{key1:key2}} {{endif}}{{key1:key3}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '3',
            ],
            [
                ' {{if(key1:key2)}}{{key1:key2}}{{if(key1:key)}} {{key1:key3}}{{endif}}'
                . '{{if(key1:key4)}} {{key1:key4}}{{endif}}{{endif}} ',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
                ' 2 4 ',
            ],
            [
                '{{if(key1:key4)}}{{key1:key2}}{{else}}{{key1:key3}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '3',
            ],
            [
                '{{if(key1:key5)}}{{key1:key2}}{{elseif(key1:key4)}}{{key1:key4}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
                '4',
            ],
            [
                'beforeText {{if(key1:key5)}}{{key1:key2}}{{elseif(key1:key4)}}{{key1:key4}}{{endif}} afterText',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
                'beforeText 4 afterText',
            ],
            [
                'beforeText {{if(true)}}{{key1}}{{endif}} afterText',
                [
                    'key1' => 1,
                ],
                'beforeText 1 afterText',
            ],
            [
                '{{if(key1:key2 && key1:key3)}}{{key1:key2}} {{key1:key3}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '2 3',
            ],
            [
                '{{if(key1:key2)}}{{key1:key2}}{{if(false)}}{{key1:key3}}{{else}} else{{endif}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '2 else',
            ],
            [
                '{{if(key1:key2&&key1:key3)}}{{key1:key2}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                    ],
                ],
                '2',
            ],
            [
                '{{|spf(key1 , key2)}}',
                [
                    1,
                    'key1' => false,
                    'key2' => true,
                ],
                '1',
            ],
            [
                '{{|spf(key1,key2)}}',
                [
                    1,
                    'key1' => false,
                    'key2' => true,
                ],
                '1',
            ],
            [
                '{{|sprintf()}}',
                [
                    null,
                ],
                'null',
            ],
            [
                '{{if(foo)}}{{bar}}{{else}}false{{endif}}',
                [
                    'foo' => true,
                    'bar' => 'bar',
                ],
                'bar',
            ],
            [
                '{{foo|spf(0, 1)}}',
                [
                    'foo' => 'foo',
                ],
                'foo',
            ],
            [
                'test {{key\:1:key2}}',
                [
                    'key:1' => [
                        'key2' => 'foo',
                    ],
                ],
                'test foo',
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
        Formatter::addSpecifier('spf', Specifier::class);
        setlocale(LC_ALL, 'en_US.UTF-8');
        $this->assertEquals($equal, Formatter::format($string, $values));
    }

    /**
     * Данные для метода Formatter::format
     *
     * @return mixed[]
     */
    public function dataFormatConditionExceptions(): array
    {
        return [
            [
                '{{if(key1)}}{{key1:key2}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
            ],
            [
                '{{if(key1){{key1:key2}}{{endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
            ],
            [
                '{{if(key1)}}{{key1:key2}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
            ],
            [
                '{{if(key1)}}{{key1:key2}}endif}}',
                [
                    'key1' => [
                        'key2' => 2,
                        'key3' => 3,
                        'key4' => 4,
                    ],
                ],
            ],
            [
                '{{if(key1)}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf( , )}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("0d)}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("0d", "0d" "0d")}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf(}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf(" }}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("0d" , }}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if(key1}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if()}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1',
                [
                    'key1' => 1,
                ],
            ],
            [
                'key1}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if( )}}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1}',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf(',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf( ',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{key1|sprintf("123"',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if',
                [
                    'key1' => 1,
                ],
            ],
            [
                '{{if ',
                [
                    'key1' => 1,
                ],
            ],
        ];
    }

    /**
     * Форматирование и подстановка значений в строке
     *
     * @param mixed[]  $values
     *
     * @dataProvider dataFormatConditionExceptions
     */
    public function testFormatConditionExceptions(string $string, array $values): void
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
        $this->expectException(FormatErrorException::class);
        Formatter::format($string, $values);
    }

    /**
     * Исключение при отсутсвии спецификатора
     */
    public function testSpecifierNotFoundException(): void
    {
        $this->expectException(SpecifierNotFoundException::class);
        Formatter::format('{{|unknown()}}', [1]);
    }

    /**
     * Тестирование методов работы со спецификаторами
     */
    public function testSpecifier(): void
    {
        $this->assertTrue(Formatter::addSpecifier('spf_test', Specifier::class));
        $this->assertFalse(Formatter::addSpecifier('spf_test', Specifier::class));
        $this->assertTrue(Formatter::hasSpecifier('spf_test'));
        $this->assertFalse(Formatter::hasSpecifier('unknown'));
        $this->assertInstanceOf(ISpecifier::class, Formatter::getSpecifier('spf_test'));
        $this->assertFalse(Formatter::deleteSpecifier('unknown'));
        $this->assertTrue(Formatter::deleteSpecifier('spf_test'));
    }

    /**
     * Тестирование методов работы со спецификаторами (исключение при пустом значении)
     */
    public function testAddSpecifierEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::addSpecifier('', Specifier::class);
    }

    /**
     * Тестирование методов работы со спецификаторами (исключение)
     */
    public function testAddSpecifierNotImplements(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::addSpecifier('spf_test', static::class);
    }

    /**
     * Тестирование методов работы со спецификаторами (исключение при пустом значении)
     */
    public function testHasSpecifierEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::hasSpecifier('');
    }

    /**
     * Тестирование методов работы со спецификаторами (исключение при пустом значении)
     */
    public function testDeleteSpecifierEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::deleteSpecifier('');
    }

    /**
     * Тестирование методов работы со спецификаторами (исключение при пустом значении)
     */
    public function testGetSpecifierEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::getSpecifier('');
    }

    /**
     * Тестирование методов работы с сокращениями
     */
    public function testShortcutMethods(): void
    {
        $this->assertTrue(Formatter::addShortcut('spf', 'sprintf("\'.9d")'));
        $this->assertFalse(Formatter::addShortcut('spf', Specifier::class));
        $this->assertTrue(Formatter::hasShortcut('spf'));
        $this->assertFalse(Formatter::hasShortcut('unknown'));
        $this->assertFalse(Formatter::deleteShortcut('unknown'));
        $this->assertTrue(Formatter::deleteShortcut('spf'));
    }

    /**
     * Тестирование методов работы с сокращениями
     */
    public function testShortcut(): void
    {
        $this->assertTrue(Formatter::addShortcut('spf', 'sprintf("\'.9d")'));
        $this->assertEquals(
            'test ......123......123 test',
            Formatter::format('test {{|~spf}}{{0|~spf}} test', [123])
        );
        $this->assertTrue(Formatter::deleteShortcut('spf'));
    }

    /**
     * Тестирование методов работы с сокращениями
     */
    public function testShortcutNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{|~spf}}', [123]);
    }

    /**
     * Тестирование методов работы с сокращениями (исключение при пустом значении)
     */
    public function testAddShortcutEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::addShortcut('', 'sprintf("\'.9d")');
    }

    /**
     * Тестирование методов работы с сокращениями (исключение при пустом значении)
     */
    public function testAddShortcutEmptySpecifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::addShortcut('spf', '');
    }

    /**
     * Тестирование методов работы с сокращениями (исключение при пустом значении)
     */
    public function testHasShortcutEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::hasShortcut('');
    }

    /**
     * Тестирование методов работы с сокращениями (исключение при пустом значении)
     */
    public function testDeleteShortcutEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::deleteShortcut('');
    }
}
