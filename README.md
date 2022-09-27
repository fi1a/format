# Форматирование строковых шаблонов

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

Библиотека предоставляет возможность форматирования строковых шаблонов с использованием спецификаторов.

## Установка

Установить этот пакет можно как зависимость, используя Composer.

``` bash
composer require fi1a/format
```

**Внимание!** Совместимость версий не гарантируется при переходе major или minor версии.
Указывайте допустимую версию пакета в своем проекте следующим образом: ```"fi1a/format": ">=1.1.0 <1.2.0"```.

## Использование

```php
use Fi1a\Format\Formatter;

Formatter::format('[{{user:login}}] - {{user:name}}', ['user' => ['name' => 'John', 'login' => 'john85']]); // [john85] - John

Formatter::format('{{0}}, {{1}}',[1, 2]); // 1, 2

Formatter::format('{{}}, {{}}',[1, 2]); // 1, 2

Formatter::format('{{foo}}, {{foo}}, {{foo}}', ['foo' => 'bar',]); // bar, bar, bar
```

При отсутствии ключа в массиве значений выбрасывается исключение ```\Fi1a\Format\AST\Exception\NotFoundKey```

```php
use Fi1a\Format\Formatter;
use Fi1a\Format\AST\Exception\NotFoundKey;

try {
    Formatter::format('{{not_exists}}', []);
} catch (NotFoundKey $exception) {
    
}
```

### Использование функций спецификаторов

Указание функции спецификатора следует после указания ключа с разделителем "|".

Пример:

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}',[2016, 2, 27,]); // 2016-02-27
```

#### C доступом по пути

Строковый шаблон содержит путь до значения в ассоциативном массиве.

```php
use Fi1a\Format\Formatter;

Formatter::format('[{{user:login}}] - {{user:name}}', ['user' => ['name' => 'John', 'login' => 'john85']]); // [john85] - John
```

#### C доступом по индексам

Строковый шаблон содержит индексы значений в массиве.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0}}, {{1}}',[1, 2]); // 1, 2
Formatter::format('{{}}, {{}}',[3, 4]); // 3, 4
```

#### Спецификаторы функции sprintf

Указание функции спецификатора следует после указания ключа с разделителем "|".

Спецификаторы используемые в функции [sprintf](https://www.php.net/manual/ru/function.sprintf.php).

* **b** - Аргумент рассматривается как целое число и печатается в бинарном представлении.
* **c** - Аргумент рассматривается как целое число и печатается как символ из таблицы ASCII с соответствующим кодом.
* **d** - Аргумент рассматривается как целое число и печатается как целое число со знаком.
* **e** - Аргумент считается за число в научной нотации (т.е. 1.2e+2). Спецификатор точности задает количество цифр после десятичной запятой. В более ранних версиях он задавал общее количество значащих цифр (т.е. после запятой выводилось на 1 символ меньше).
* **E** - Аналогично спецификатору e, но использует заглавные символы (т.е. 1.2E+2).
* **f** - Аргумент считается за число с плавающей точкой (с учетом локали).
* **F** - Аргумент считается за число с плавающей точкой (без учета локали). Доступно с PHP 5.0.3.
* **o** - Аргумент рассматривается как целое число и печатается в восмеричном представлении.
* **s** - Аргумент рассматривается и печатается как строка.
* **u** - Аргумент рассматривается как целое число и печатается как беззнаковое целое число.
* **x** - Аргумент рассматривается как целое число и печатается в шестнадцатеричном представлении (буквы будут в нижнем регистре).
* **X** - Аргумент рассматривается как целое число и печатается в шестнадцатеричном представлении (буквы будут в верхнем регистре).

#### Использование символа заполнения

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("\'.9d")}}', [123]); // ......123
Formatter::format('{{0|sprintf("\'.09d")}}', [123]); // 000000123
```

#### Целое с лидирующими нулями

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}', [2020, 6, 7]); // 2020-06-07
```

#### Форматирование денежных единиц

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("01.2f")}}', [100.5]); // 100.50
```

#### Спецификатор из массива со значениями

Спецификатор можно динамически задавать через массив со значениями.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|sprintf(modifier)}}', ['value' => 100.5, 'modifier' => "01.2f"]); // 100.50
```

### Условные конструкции

Доступны следующий условные конструкции: ```if, elseif, else, endif```.
При отсутствии ключа в массиве значений исключение не выбрасывается.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{if(foo)}}{{bar}}{{else}}false{{endif}}', ['foo' => true, 'bar' => 'bar']); // bar

Formatter::format('{{if(not_exists)}}{{foo}}{{elseif(bar)}}{{bar}}{{endif}}', ['foo' => 'foo', 'bar' => 'bar']); // bar
```

[badge-release]: https://img.shields.io/packagist/v/fi1a/format?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/format?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/format?style=flat-square
[badge-build]: https://img.shields.io/travis/com/fi1a/format?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/github/fi1a/format/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/format.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/fi1a/format
[license]: https://github.com/fi1a/format/blob/master/LICENSE
[php]: https://php.net
[build]: https://app.travis-ci.com/github/fi1a/format
[coverage]: https://coveralls.io/r/fi1a/format?branch=master
[downloads]: https://packagist.org/packages/fi1a/format
