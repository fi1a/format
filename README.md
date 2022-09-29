# Форматирование строковых шаблонов

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

Библиотека предоставляет возможность форматирования строковых шаблонов с использованием функций спецификаторов.

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

### Использование функций спецификаторов

Доступные функции спецификаторов:

- date - форматирование даты и времени;
- sprintf - форматирование строки;
- escape - преобразует специальные символы в HTML-сущности;

Указание функции спецификатора следует после указания ключа с разделителем "|".

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}',[2016, 2, 27,]); // 2016-02-27
```

Модификаторы функций спецификаторов можно динамически задавать через массив со значениями.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|sprintf(modifier)}}', ['value' => 100.5, 'modifier' => "01.2f"]); // 100.50
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

##### Использование символа заполнения

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("\'.9d")}}', [123]); // ......123
Formatter::format('{{0|sprintf("\'.09d")}}', [123]); // 000000123
```

##### Целое с лидирующими нулями

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}', [2020, 6, 7]); // 2020-06-07
```

##### Форматирование денежных единиц

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("01.2f")}}', [100.5]); // 100.50
```

#### Спецификаторы функции date

Указание функции спецификатора следует после указания ключа с разделителем "|".

Формат модификатора используемых в функции:

День
* **d** - День месяца, 2 цифры с ведущим нулём (от 01 до 31).
* **D** - Текстовое представление дня недели (от Пн до Вс).
* **j** - День месяца без ведущего нуля (от 1 до 31).
* **l** - Полное наименование дня недели (от Понедельник до Воскресенье).
* **N** - Порядковый номер дня недели в соответствии со стандартом ISO 8601 (от 1 (понедельник) до 7 (воскресенье)).
* **S** - Английский суффикс порядкового числительного дня месяца, 2 символа (st, nd, rd или th. Применяется совместно с j).
* **w** - Порядковый номер дня недели (от 0 (воскресенье) до 6 (суббота)).
* **z** - Порядковый номер дня в году (начиная с 0) (От 0 до 365).

Неделя

* **W** - Порядковый номер недели года в соответствии со стандартом ISO 8601; недели начинаются с понедельника (начиная с 0) (Например: 42 (42-я неделя года)).

Месяц

* **F** - Полное наименование месяца, например, Января или Марта (от Января до Марта).
* **f** - Полное наименование месяца, например, Январь или Март (от Январь до Март).
* **m** - Полное наименование месяца, например, January или March (от 01 до 12).
* **M** - Сокращённое наименование месяца, 3 символа (от Янв до Дек).
* **n** - Порядковый номер месяца без ведущего нуля (от 1 до 12).
* **t** - Количество дней в указанном месяце (от 28 до 31).

Год

* **L** - Признак високосного года (1, если год високосный, иначе 0.).
* **o** - Номер года в соответствии со стандартом ISO 8601. Имеет то же значение, что и Y, кроме случая, когда номер недели ISO (W) принадлежит предыдущему или следующему году; тогда будет использован год этой недели. (Примеры: 1999 или 2003).
* **X** - Расширенное полное числовое представление года, не менее 4 цифр, с - для годов до нашей эры и + для годов нашей эры. (Примеры: -0055, +0787, +1999, +10191).
* **x** - Расширенное полное числовое представление, если требуется или стандартное полное числовое представление, если возможно (например, Y). Не менее четырёх цифр. Для годов до нашей эры указан префикс -. У годов после (и включая) 10000 префикс +. (Примеры: -0055, 0787, 1999, +10191).
* **Y** - Полное числовое представление года, не менее 4 цифр, с - для годов до нашей эры. (Примеры: -0055, 0787, 1999, 2003, 10191.).
* **y** - Номер года, 2 цифры (Примеры: 99, 03).

Время

* **a** - Ante meridiem (лат. "до полудня") или Post meridiem (лат. "после полудня") в нижнем регистре (am или pm).
* **A** - Ante meridiem или Post meridiem в верхнем регистре (AM или PM).
* **B** - Время в формате Интернет-времени (альтернативной системы отсчёта времени суток) (от 000 до 999).
* **g** - Часы в 12-часовом формате без ведущего нуля (от 1 до 12).
* **G** - Часы в 24-часовом формате без ведущего нуля (от 0 до 23).
* **h** - Часы в 12-часовом формате с ведущим нулём (от 01 до 12).
* **H** - Часы в 24-часовом формате с ведущим нулём (от 00 до 23).
* **i** - Минуты с ведущим нулём (от 00 до 59).
* **s** - Секунды с ведущим нулём (от 00 до 59).
* **u** - Микросекунды. (Например: 654321).
* **v** - Микросекунды. (Пример: 654).
* **v** - Микросекунды. (Пример: 654).

Часовой пояс

* **e** - Идентификатор часового пояса (Примеры: UTC, GMT, Atlantic/Azores).
* **I** - Признак летнего времени (1, если дата соответствует летнему времени, 0 в противном случае.).
* **O** - Разница с временем по Гринвичу без двоеточия между часами и минутами (Например: +0200).
* **P** - Разница с временем по Гринвичу с двоеточием между часами и минутами (Например: +02:00).
* **p** - То же, что и P, но возвращает Z вместо +00:00 (доступен, начиная с PHP 8.0.0) (Например: +02:00).
* **T** - Аббревиатура часового пояса, если известна; в противном случае смещение по Гринвичу. (Примеры: EST, MDT, +05).
* **Z** - Смещение часового пояса в секундах. Для часовых поясов, расположенных западнее UTC, возвращаются отрицательные числа, а для расположенных восточнее UTC - положительные. (от -43200 до 50400).

Полная дата/время

* **c** - Дата в формате стандарта ISO 8601 (2004-02-12T15:19:21+00:00).
* **r** - Дата в формате » RFC 222/» RFC 5322 (Например: Thu, 21 Dec 2000 16:01:07 +0200).
* **U** - Количество секунд, прошедших с начала Эпохи Unix (1 января 1970 00:00:00 GMT) (Смотрите также time()).

```php
use Fi1a\Format\Formatter;

Formatter::format('{{foo|date("d.m.Y")}}', ['foo' => time()]); // 28.09.2022
```

#### Функция спецификатор escape

Преобразует специальные символы в HTML-сущности `{{|escape(flag, encoding, doubleEncode)}}`

```php
use Fi1a\Format\Formatter;

Formatter::format('{{|escape}}', ['"test"']); // &amp;quot;test&amp;quot;
```


```php
use Fi1a\Format\Specifier\Date;
use Fi1a\Format\Formatter;

Formatter::format('{{foo|date}}', ['foo' => time()]); // 28.09.2022 07:06:00
Date::setDefaultFormat('d.m.Y');
Formatter::format('{{foo|date}}', ['foo' => time()]); // 28.09.2022
```


преобразует специальные символы в HTML-сущности




### Условные конструкции

Доступны следующий условные конструкции: ```if, elseif, else, endif```.
При отсутствии ключа в массиве значений исключение не выбрасывается.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{if(foo)}}{{bar}}{{else}}false{{endif}}', ['foo' => true, 'bar' => 'bar']); // bar

Formatter::format('{{if(not_exists)}}{{foo}}{{elseif(bar)}}{{bar}}{{endif}}', ['foo' => 'foo', 'bar' => 'bar']); // bar
```

### Добавление функций спецификаторов

Класс функции спецификатора должен реализовывать интерфейс ```\Fi1a\Format\Specifier\ISpecifier```

Добавление новой функции спецификатора осуществляется с помощью метода ```Fi1a\Format\Formatter::addSpecifier()```:

```php
use Fi1a\Format\Formatter;

Formatter::addSpecifier('spf', Specifier::class); // true

Formatter::format('{{foo|spf(true)}}', ['foo' => 'foo']); // foo
```

### Сокращения

Для упрощения ввода функций спецификаторов можно использовать сокращения.
Сокращения добавляются методом `Formatter::addShortcut` с указанием имени сокращения.
Для использования сокращения, введите название начиная с символа "~".

```php
use Fi1a\Format\Formatter;

Formatter::addShortcut('dt', 'date("d.m.Y", "en")');
Formatter::format('{{foo|~dt}}', ['foo' => time()]); // 29.09.2022
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