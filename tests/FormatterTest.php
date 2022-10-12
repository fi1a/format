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
            // 0
            [
                '{{}}',
                ['value'],
                'value',
            ],
            // 1
            [
                '{{   }}',
                ['value'],
                'value',
            ],
            // 2
            [
                '{{}} {{}}',
                ['value1', 'value2'],
                'value1 value2',
            ],
            // 3
            [
                '{{}} {{0}} {{}}',
                ['value1', 'value2'],
                'value1 value1 value2',
            ],
            // 4
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
            // 5
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
            // 6
            [
                '{{fix:fix3:fix4:fix5}} {{fix:fix3}}{{fix}}',
                [
                    'fix' => new FormatClass(),
                ],
                '5 arrayFIX',
            ],
            // 7
            [
                'text {{|sprintf}} text',
                ['value'],
                'text value text',
            ],
            // 8
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
            // 9
            [
                'text {{|sprintf()}} text',
                ['value'],
                'text value text',
            ],
            // 10
            [
                '{{fix:fix1}} {{fix:fix2|sprintf("1.1f")}}',
                [
                    'fix' => new FormatClass(),
                ],
                'string 20.0',
            ],
            // 11
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
            // 12
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t |sprintf('1.1f')}}",
                [
                    'key1' => 10,
                    'key2' => 20,
                ],
                '20, 10.0',
            ],
            // 13
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
            // 14
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t |sprintf('02d')}}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 01',
            ],
            // 15
            [
                "{{key2|sprintf('d')}}, {{\n key1 \t | \t sprintf('02d')\n }}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 01',
            ],
            // 16
            [
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1,
                    'key2' => 2,
                ],
                '2, 1',
            ],
            // 17
            [
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            // 18
            [
                "{{key2}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    'key2' => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            // 19
            [
                "{{|sprintf('s')}}, {{\n key1 \t }}",
                [
                    'key1' => 1.1111,
                    0 => 2.2222,
                ],
                '2.2222, 1.1111',
            ],
            // 20
            [
                "{{0|sprintf('\'.9d')}}",
                [
                    123,
                ],
                '......123',
            ],
            // 21
            [
                "%b = '{{0|sprintf('b')}}'",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\'',
            ],
            // 22
            [
                "%%b%%% = '{{0|sprintf('b')}}'",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\'',
            ],
            // 23
            [
                "%%b%%% = '{{0|sprintf('b')}}' %d%%%d\$d%",
                [
                    43951789,
                ],
                '%%b%%% = \'10100111101010011010101101\' %d%%%d$d%',
            ],
            // 24
            [
                "%b = '{{0|sprintf('b')}}' $0%d",
                [
                    43951789,
                ],
                '%b = \'10100111101010011010101101\' $0%d',
            ],
            // 25
            [
                "%c = '{{0|sprintf('c')}}'",
                [
                    65,
                ],
                '%c = \'A\'',
            ],
            // 26
            [
                "%e = '{{0|sprintf('e')}}'",
                [
                    43951789,
                ],
                '%e = \'4.395179e+7\'',
            ],
            // 27
            [
                "%+d = '{{0|sprintf('+d')}}'",
                [
                    43951789,
                ],
                '%+d = \'+43951789\'',
            ],
            // 28
            [
                "%+d = '{{0|sprintf('+d')}}'",
                [
                    -43951789,
                ],
                '%+d = \'-43951789\'',
            ],
            // 29
            [
                '{{0|sprintf("10s")}}',
                [
                    'string',
                ],
                '    string',
            ],
            // 30
            [
                '{{0|sprintf("-10s")}}',
                [
                    'string',
                ],
                'string    ',
            ],
            // 31
            [
                '{{0|sprintf(\'010s\')}}',
                [
                    'string',
                ],
                '0000string',
            ],
            // 32
            [
                "{{0|sprintf('\'#10s')}}",
                [
                    'string',
                ],
                '####string',
            ],
            // 33
            [
                '{{0|sprintf(\'10.10s\')}}',
                [
                    'string value',
                ],
                'string val',
            ],
            // 34
            [
                '{{0|sprintf(\'04d\')}}-{{1|sprintf(\'02d\')}}-{{2|sprintf(\'02d\')}}',
                [
                    2016,
                    2,
                    27,
                ],
                '2016-02-27',
            ],
            // 35
            [
                '{{key}}',
                [
                    'key' => true,
                ],
                'true',
            ],
            // 36
            [
                '{{key}}',
                [
                    'key' => null,
                ],
                'null',
            ],
            // 37
            [
                '{{key}}',
                [
                    'key' => new static(),
                ],
                'Fi1a\Unit\Format\FormatterTest',
            ],
            // 38
            [
                '{{key}}',
                [
                    'key' => 0,
                ],
                '0',
            ],
            // 39
            [
                '{{}} {{}} {{}}',
                [
                    1, 2 ,3,
                ],
                '1 2 3',
            ],
            // 40
            [
                '{{}}',
                [
                    1,
                ],
                '1',
            ],
            // 41
            [
                '{{|spf(false, true, null)}}',
                [
                    1,
                ],
                '1',
            ],
            // 42
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
            // 43
            [
                '{{if ( key1:key2 ) }}{{ key1:key2 }}{{ endif }}',
                [
                    'key1' => [
                        'key2' => 2,
                    ],
                ],
                '2',
            ],
            // 44
            [
                'text{{if(key1:key2)}}{{key1:key2}}{{endif}}value',
                [
                    'key1' => [
                        'key2' => false,
                    ],
                ],
                'textvalue',
            ],
            // 45
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
            // 46
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
            // 47
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
            // 48
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
            // 49
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
            // 50
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
            // 51
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
            // 52
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
            // 53
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
            // 54
            [
                'beforeText {{if(true)}}{{key1}}{{endif}} afterText',
                [
                    'key1' => 1,
                ],
                'beforeText 1 afterText',
            ],
            // 55
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
            // 56
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
            // 57
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
            // 58
            [
                '{{|spf(key1 , key2)}}',
                [
                    1,
                    'key1' => false,
                    'key2' => true,
                ],
                '1',
            ],
            // 59
            [
                '{{|spf(key1,key2)}}',
                [
                    1,
                    'key1' => false,
                    'key2' => true,
                ],
                '1',
            ],
            // 60
            [
                '{{|sprintf()}}',
                [
                    null,
                ],
                'null',
            ],
            // 61
            [
                '{{if(foo)}}{{bar}}{{else}}false{{endif}}',
                [
                    'foo' => true,
                    'bar' => 'bar',
                ],
                'bar',
            ],
            // 62
            [
                '{{foo|spf(0, 1)}}',
                [
                    'foo' => 'foo',
                ],
                'foo',
            ],
            // 63
            [
                'test {{key\:1:key2}}',
                [
                    'key:1' => [
                        'key2' => 'foo',
                    ],
                ],
                'test foo',
            ],
            // 64
            [
                '{{foo|spf("")}}',
                [
                    'foo' => 'foo',
                ],
                'foo',
            ],
            // 65
            [
                '{{foo|spf(\'\')}}',
                [
                    'foo' => 'foo',
                ],
                'foo',
            ],
            // 66
            [
                '{{foo|spf("0")}}',
                [
                    'foo' => 'foo',
                ],
                'foo',
            ],
            // 67
            [
                'Размер файла {{if(name)}}"{{name}}" {{endif}}'
                . 'должен быть{{if(min)}} больше {{min}} Байт{{endif}}{{if(max)}}'
                . '{{if(min)}} и{{endif}} меньше {{max}} Байт{{endif}}',
                [
                    'name' => null,
                    'min' => 0,
                    'max' => 100,
                ],
                'Размер файла должен быть меньше 100 Байт',
            ],
            // 68
            [
                'Размер файла {{if(name)}}"{{name}}" {{endif}}'
                . 'должен быть{{if(min)}} больше {{min}} Байт{{endif}}{{if(max)}}'
                . '{{if(min)}} и{{endif}} меньше {{max}} Байт{{endif}}',
                [
                    'name' => null,
                    'min' => 50,
                    'max' => 100,
                ],
                'Размер файла должен быть больше 50 Байт и меньше 100 Байт',
            ],
            // 69
            [
                'Размер файла {{if(name)}}"{{name}}" {{endif}}'
                . 'должен быть{{if(min)}} больше {{min}} Байт{{endif}}{{if(max)}}'
                . '{{if(min)}} и{{endif}} меньше {{max}} Байт{{endif}}',
                [
                    'name' => null,
                    'min' => 50,
                    'max' => 0,
                ],
                'Размер файла должен быть больше 50 Байт',
            ],
            // 70
            [
                'Текст1 {{if(key1)}} key1 {{if(key2)}} key2 {{endif}} {{endif}} Текст2'
                . 'Текст3 {{if(key3)}} key3 {{if(key4)}} key4 {{endif}} {{endif}} Текст4',
                [
                    'key1' => 1,
                    'key2' => 0,
                    'key3' => 0,
                    'key4' => 1,
                ],
                'Текст1  key1   Текст2Текст3  Текст4',
            ],
            // 71
            [
                'Текст1 {{if(key1)}} key1 {{if(key2)}} key2 {{endif}} {{endif}} Текст2'
                . 'Текст3 {{if(key3)}} key3 {{if(key4)}} key4 {{endif}} {{else}} else1 {{endif}} Текст4',
                [
                    'key1' => 1,
                    'key2' => 0,
                    'key3' => 0,
                    'key4' => 1,
                ],
                'Текст1  key1   Текст2Текст3  else1  Текст4',
            ],
            // 72
            [
                '1{{if(key1)}}2{{if(key2)}}3{{endif}}{{else}}4{{endif}}',
                [
                    'key1' => 1,
                    'key2' => 0,
                ],
                '12',
            ],
            // 73
            [
                '1{{if(key1)}}2{{if(key2)}}3{{endif}}{{else}}4{{endif}}',
                [
                    'key1' => 1,
                    'key2' => 1,
                ],
                '123',
            ],
            // 74
            [
                '1{{if(key1)}}2{{if(key2)}}3{{endif}}5{{if(key3)}}6{{endif}}{{else}}4{{endif}}',
                [
                    'key1' => 1,
                    'key2' => 1,
                    'key3' => 0,
                ],
                '1235',
            ],
            // 75
            [
                '1{{if(key1)}}2{{if(key2)}}3{{endif}}5{{if(key3)}}6{{endif}}{{else}}4{{endif}}',
                [
                    'key1' => 1,
                    'key2' => 1,
                    'key3' => 1,
                ],
                '12356',
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
            [
                '{{key1|specifier("100" ',
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
