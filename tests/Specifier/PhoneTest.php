<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Specifier;

use Fi1a\Format\Formatter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Форматирование телефона
 */
class PhoneTest extends TestCase
{
    /**
     * Форматирование телефона
     */
    public function testPhone(): void
    {
        $this->assertEquals(
            '+7(922)822-3576',
            Formatter::format('{{value|phone("+7(ddd)ddd-dddd")}}', ['value' => '+79228223576'])
        );
        $this->assertEquals(
            '+7(922)822-3576',
            Formatter::format('{{value|phone("+7(ddd)ddd-dddd")}}', ['value' => '+7(922)8223576'])
        );
        $this->assertEquals(
            '+7(922)822-3576',
            Formatter::format('{{value|phone("+7(ddd)ddd-dddd")}}', ['value' => '9228223576'])
        );
        $this->assertEquals(
            '+7(922)822-3576',
            Formatter::format('{{value|phone("+d(ddd)ddd-dddd")}}', ['value' => '+7(922)8223576'])
        );
        $this->assertEquals(
            '+7 922 822 3576',
            Formatter::format('{{value|phone("+d ddd ddd dddd")}}', ['value' => '+7-922-822-3576'])
        );
        $this->assertEquals(
            '+7-922-822-3576',
            Formatter::format('{{value|phone("+d-ddd-ddd-dddd")}}', ['value' => '+7-922-822-3576'])
        );
        $this->assertEquals(
            '(6783)44-00-44',
            Formatter::format('{{value|phone("(dddd)dd-dd-dd")}}', ['value' => '(6783)44-00-44'])
        );
        $this->assertEquals(
            '(6783)44-00-44',
            Formatter::format('{{value|phone("(dddd)dd-dd-dd")}}', ['value' => '6783440044'])
        );
        $this->assertEquals(
            '(6783)',
            Formatter::format('{{value|phone("(dddd)")}}', ['value' => '6783440044'])
        );
        $this->assertEquals(
            '(6783)440044',
            Formatter::format('{{value|phone("(dddd)dddddddd")}}', ['value' => '6783440044'])
        );
        $this->assertEquals(
            '(6783)44-00-44',
            Formatter::format('{{value|phone("(6783)dd-dd-dd")}}', ['value' => '6783440044'])
        );
        $this->assertEquals(
            '(5677)67-83-44',
            Formatter::format('{{value|phone("(5677)dd-dd-dd")}}', ['value' => '6783440044'])
        );
    }

    /**
     * Исключение при двух модификаторах
     */
    public function testPhoneException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{value|phone("+d ddd ddd dddd", "")}}', ['value' => '+7-922-822-3576']);
    }

    /**
     * Исключение при отсутсвии модификатора
     */
    public function testPhoneExceptionEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{value|phone()}}', ['value' => '+7-922-822-3576']);
    }

    /**
     * Исключение при отсутсвии модификатора
     */
    public function testPhoneExceptionEmptyPhoneFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Formatter::format('{{value|phone("")}}', ['value' => '+7-922-822-3576']);
    }
}
