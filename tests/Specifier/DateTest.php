<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\Date;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use const LC_ALL;

/**
 * Спецификатор функции date
 */
class DateTest extends TestCase
{
    /**
     * Исключение при более чем одном модификаторе
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format(
            '{{|date(now1, now2, now3)}}',
            [time()],
            ['now1' => 'd.m.Y', 'now2' => 'h:i:s', 'now3' => false]
        );
    }

    /**
     * Форматирование и подстановка значений в строке
     *
     * @return mixed[]
     */
    public function dataProviderFormat(): array
    {
        $time = time();

        return [
            [
                '{{|date}}',
                [
                    $time,
                ],
                date('d.m.Y H:i:s', $time),
            ],
            [
                '{{|date}}',
                [
                    '28.09.2022 06:25:00',
                ],
                '28.09.2022 06:25:00',
            ],
            [
                '{{|date("d.m.Y")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                '28.09.2022',
            ],
            [
                '{{|date("D")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                'Ср',
            ],
            [
                '{{|date("l")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                'Среда',
            ],
            [
                '{{|date("F")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                'Сентября',
            ],
            [
                '{{|date("f")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                'Сентябрь',
            ],
            [
                '{{|date("M")}}',
                [
                    '28.09.2022 06:25:00',
                ],
                'Сен',
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
        setlocale(LC_ALL, 'en_US.UTF-8');
        $this->assertEquals($equal, Formatter::format($string, $values));
    }

    /**
     * Формат по умолчанию
     */
    public function testDefaultFormat(): void
    {
        Date::setDefaultFormat('d.m.Y');
        $this->assertEquals(
            '28.09.2022',
            Formatter::format('{{foo|date}}', ['foo' => '28.09.2022 07:04:21'])
        );
    }

    /**
     * Локализация
     */
    public function testSetLocalization(): void
    {
        $this->assertTrue(Date::setDefaultLocale('ru'));

        $this->assertTrue(Date::setDayOfWeek3('ru', [
            'Mon' => 'Пн',
            'Tue' => 'Вт',
            'Wed' => 'Ср',
            'Thu' => 'Чт',
            'Fri' => 'Пт',
            'Sat' => 'Сб',
            'Sun' => 'Вс',
        ]));

        $this->assertTrue(Date::setDayOfWeek('ru', [
            'Monday' => 'Понедельник',
            'Tuesday' => 'Вторник',
            'Wednesday' => 'Среда',
            'Thursday' => 'Четверг',
            'Friday' => 'Пятница',
            'Saturday' => 'Суббота',
            'Sunday' => 'Воскресенье',
        ]));

        $this->assertTrue(Date::setFullMonthName('ru', [
            'January' => 'Января',
            'February' => 'Февраля',
            'March' => 'Марта',
            'April' => 'Апреля',
            'May' => 'Мая',
            'June' => 'Июня',
            'July' => 'Июля',
            'August' => 'Августа',
            'September' => 'Сентября',
            'October' => 'Октября',
            'November' => 'Ноября',
            'December' => 'Декабря',
        ]));

        $this->assertTrue(Date::setFullMonthNameAccusative('ru', [
            'January' => 'Январь',
            'February' => 'Февраль',
            'March' => 'Марть',
            'April' => 'Апрель',
            'May' => 'Май',
            'June' => 'Июнь',
            'July' => 'Июль',
            'August' => 'Август',
            'September' => 'Сентябрь',
            'October' => 'Октябрь',
            'November' => 'Ноябрь',
            'December' => 'Декабрь',
        ]));

        $this->assertTrue(Date::setMonthName3('ru', [
            'Jan' => 'Янв',
            'Feb' => 'Фев',
            'Mar' => 'Мар',
            'Apr' => 'Апр',
            'May' => 'Май',
            'Jun' => 'Июн',
            'Jul' => 'Июл',
            'Aug' => 'Авг',
            'Sep' => 'Сен',
            'Oct' => 'Окт',
            'Nov' => 'Ноя',
            'Dec' => 'Дек',
        ]));
    }

    /**
     * Исключение при пустом аргументе в методе setDefaultLocale
     */
    public function testSetDefaultLocaleEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setDefaultLocale('');
    }

    /**
     * Исключение при пустом аргументе в методе setDefaultFormat
     */
    public function testSetDefaultFormatEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setDefaultFormat('');
    }

    /**
     * Исключение при пустом аргументе в методе setDayOfWeek3
     */
    public function testSetDayOfWeek3EmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setDayOfWeek3('', []);
    }

    /**
     * Исключение при пустом аргументе в методе setDayOfWeek
     */
    public function testSetDayOfWeekEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setDayOfWeek('', []);
    }

    /**
     * Исключение при пустом аргументе в методе setFullMonthName
     */
    public function testSetFullMonthNameEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setFullMonthName('', []);
    }

    /**
     * Исключение при пустом аргументе в методе setFullMonthNameAccusative
     */
    public function testSetFullMonthNameAccusativeEmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setFullMonthNameAccusative('', []);
    }

    /**
     * Исключение при пустом аргументе в методе setMonthName3
     */
    public function testSetMonthName3EmptyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Date::setMonthName3('', []);
    }
}
