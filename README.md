# PHP форматирование строковых шаблонов

[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![Coverage Status][badge-coverage]
[![Total Downloads][badge-downloads]][downloads]
[![Support mail][badge-mail]][mail]

Пакет предоставляет возможность форматирования строковых шаблонов с использованием функций спецификаторов.

Возможности пакета:

- форматирование строки и числа;
- форматирование даты и времени на русском языке (месяцы и дни недели);
- преобразование специальных символов в HTML-сущности;
- преобразование HTML-сущностей обратно в соответствующие символы;
- форматирование размера памяти;
- форматирование времени;
- склонение слов после числительных;
- форматирование номера телефона;
- форматирование цены;
- условные конструкции (if, elseif, else, endif);
- собственные функции спецификаторы;
- возможность задать сокращения для функций спецификаторов;
- применение функций спецификаторов цепочкой к одному значению.

## Установка

Установить этот пакет можно как зависимость, используя Composer.

``` bash
composer require fi1a/format
```

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

Все значения преобразовываются в специальные символы HTML-сущности, с помощью функции `htmlspecialchars`.
Для того чтобы преобразовать обратно, воспользуйтесь функцией спецификатором `unescape` или передайте четвертым аргументом
значение `false` отменяющее автоматическое преобразование специальных символов в HTML-сущности.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value}}', ['value' => '&']); // "&amp;"

Formatter::format('{{value|unescape}}', ['value' => '&']); // "&"
// или
Formatter::format('{{value}}', ['value' => '&'], [], false); // "&"
```

### C доступом по пути

Строковый шаблон содержит путь до значения в ассоциативном массиве.

```php
use Fi1a\Format\Formatter;

Formatter::format('[{{user:login}}] - {{user:name}}', ['user' => ['name' => 'John', 'login' => 'john85']]); // [john85] - John
```

### C доступом по индексам

Строковый шаблон содержит индексы значений в массиве.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0}}, {{1}}',[1, 2]); // 1, 2
Formatter::format('{{}}, {{}}',[3, 4]); // 3, 4
```

## Экранирование спец. символов

Для экранирования спец. символов используется символ "\\".

```php
use Fi1a\Format\Formatter;

Formatter::format('\\{{0\\}}', [0 => 'foo']); // {{0}}
Formatter::format('{{0}}', [0 => 'foo']); // foo
```

Для экранирования всех спец. символов в строке,
можно воспользоваться функцией ```escape``` класса ```Fi1a\Format\Safe```:

```php
use Fi1a\Format\Formatter;
use Fi1a\Format\Safe;

Formatter::format(Safe::escape(('{{0}}'), [0 => 'foo']); // {{0}}
```

Для того чтобы убрать экранирование спец. символов в строке,
можно воспользоваться функцией ```unescape``` класса ```Fi1a\Format\Safe```:

```php
use Fi1a\Format\Formatter;
use Fi1a\Format\Safe;

Formatter::format(Safe::unescape(('\\{{0}}'), [0 => 'foo']); // foo
```

## Использование функций спецификаторов

Доступные функции спецификаторов:

- date - форматирование даты и времени;
- sprintf - форматирование строки и чисел;
- escape - преобразует специальные символы в HTML-сущности;
- unescape - преобразует специальные HTML-сущности обратно в соответствующие символы;
- memory - форматирование размера памяти (x.x Б, x.x КБ, x.x МБ, x.x ГБ, x.x ТБ, x.x ПБ);
- time - форматирование времени (< 1 сек., x сек., x мин., x ч., x д.);
- declension - склонение слов после числительных;
- phone - форматирование номера телефона (+7(ddd)ddd-dddd);
- price - форматирование цены.

Указание функции спецификатора следует после указания ключа с разделителем "|".

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}',[2016, 2, 27,]); // 2016-02-27
```

Модификаторы функций спецификаторов можно динамически задавать через массив передаваемый третьим аргументом функции `format`.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|sprintf(modifier)}}', ['value' => 100.5], ['modifier' => '01.2f']); // 100.50
```

Возможно указание функций спецификатора цепочкой с разделителем "|". Тогда значение будет передаваться последовательно,
по цепочке, от одной функции спецификатора к другой.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|unescape|sprintf|escape}}', ['value' => 'a&b']); // "a&amp;b"
```

## PHP форматирование строки и числа с помощью sprintf

Спецификаторы функции sprintf. Указание функции спецификатора следует после указания ключа с разделителем "|".

Форматирование строк и чисел. Спецификаторы используемые в функци [sprintf](https://www.php.net/manual/ru/function.sprintf.php).

* **b** - Аргумент рассматривается как целое число и печатается в бинарном представлении.
* **c** - Аргумент рассматривается как целое число и печатается как символ из таблицы ASCII с соответствующим кодом.
* **d** - Аргумент рассматривается как целое число и печатается как целое число со знаком.
* **e** - Аргумент считается за число в научной нотации (т.е. 1.2e+2). Спецификатор точности задает количество цифр после десятичной запятой. В более ранних версиях он задавал общее количество значащих цифр (т.е. после запятой выводилось на 1 символ меньше).
* **E** - Аналогично спецификатору e, но использует заглавные символы (т.е. 1.2E+2).
* **f** - Аргумент считается за число с плавающей точкой (с учетом локали).
* **F** - Аргумент считается за число с плавающей точкой (без учета локали). Доступно с PHP 5.0.3.
* **o** - Аргумент рассматривается как целое число и печатается в восьмеричном представлении.
* **s** - Аргумент рассматривается и печатается как строка.
* **u** - Аргумент рассматривается как целое число и печатается как беззнаковое целое число.
* **x** - Аргумент рассматривается как целое число и печатается в шестнадцатеричном представлении (буквы будут в нижнем регистре).
* **X** - Аргумент рассматривается как целое число и печатается в шестнадцатеричном представлении (буквы будут в верхнем регистре).

### Использование символа заполнения

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("\'.9d")}}', [123]); // ......123
Formatter::format('{{0|sprintf("\'.09d")}}', [123]); // 000000123
```

### Целое с лидирующими нулями

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|sprintf("04d")}}-{{1|sprintf("02d")}}-{{2|sprintf("02d")}}', [2020, 6, 7]); // 2020-06-07
```

## PHP форматирование даты и времени

Спецификаторы функции date, форматирование даты и времени. Указание функции спецификатора следует после указания ключа с разделителем "|".

Формат модификатора используемого в функции:

День
* **d** - День месяца, 2 цифры с ведущим нулём (от 01 до 31).
* **D** - Текстовое представление дня недели на русском языке (от Пн до Вс).
* **j** - День месяца без ведущего нуля (от 1 до 31).
* **l** - Полное наименование дня недели на русском языке (от Понедельник до Воскресенье).
* **N** - Порядковый номер дня недели в соответствии со стандартом ISO 8601 (от 1 (понедельник) до 7 (воскресенье)).
* **S** - Английский суффикс порядкового числительного дня месяца, 2 символа (st, nd, rd или th. Применяется совместно с j).
* **w** - Порядковый номер дня недели (от 0 (воскресенье) до 6 (суббота)).
* **z** - Порядковый номер дня в году (От 0 до 365).

Неделя

* **W** - Порядковый номер недели года в соответствии со стандартом ISO 8601; недели начинаются с понедельника, начиная с 0. Например: 42 (42-я неделя года).

Месяц

* **F** - Полное наименование месяца на русском языке, например, "Января" или "Марта" (от "Января" до "Декабря").
* **f** - Полное наименование месяца на русском языке, например, "Январь" или "Март" (от "Январь" до "Декабрь").
* **m** - Порядковый номер месяца с ведущим нулём (от 01 до 12).
* **M** - Сокращённое наименование месяца на русском языке, 3 символа (от "Янв" до "Дек").
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

Ключи форматирования даты на русском языке

* **F** - Полное наименование месяца на русском языке, например, "Января" или "Марта" (от "Января" до "Декабря").
* **f** - Полное наименование месяца на русском языке, например, "Январь" или "Март" (от "Январь" до "Декабрь").
* **M** - Сокращённое наименование месяца на русском языке, 3 символа (от "Янв" до "Дек").
* **D** - Текстовое представление дня недели на русском языке (от "Пн" до "Вс").
* **l** - Полное наименование дня недели на русском языке (от "Понедельник" до "Воскресенье").

```php
use Fi1a\Format\Formatter;

Formatter::format('{{foo|date("d.m.Y")}}', ['foo' => time()]); // 28.09.2022
echo Formatter::format('{{|date("d F Y")}}', [time()]); // 18 Октября 2022
echo Formatter::format('{{|date("f")}}', [time()]); // Октябрь
```

Установить используемый формат по умолчанию:

```php
use Fi1a\Format\Specifier\Date;
use Fi1a\Format\Formatter;

Formatter::format('{{foo|date}}', ['foo' => time()]); // 28.09.2022 07:06:00
Date::setDefaultFormat('d.m.Y');
Formatter::format('{{foo|date}}', ['foo' => time()]); // 28.09.2022
```

## Функция спецификатор escape

Преобразует специальные символы в HTML-сущности `{{|escape(flags, encoding, doubleEncode)}}`

```php
use Fi1a\Format\Formatter;

Formatter::format('{{|escape}}', ['"test"']); // &quot;test&quot;
```

## Функция спецификатор unescape

Преобразует специальные HTML-сущности обратно в соответствующие символы `{{|escape(flags)}}`

```php
use Fi1a\Format\Formatter;

Formatter::format('{{|unescape}}', ['&amp;quot;test&amp;quot;']); // "test"
```

## PHP форматирование размера памяти

Функция спецификатор memory. Форматирование размера памяти.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{|memory}}', [1024]); // 1.0 КБ
Formatter::format('{{|memory("B")}}', [1024]); // 1024.0 Б
Formatter::format('{{|memory}}', [1024 * 1024]); // 1.0 МБ
```

## PHP форматирование времени

Функция спецификатор time, форматирование времени.

Доступные аргументы функции спецификатора:

- seconds - результат форматирования в секундах;
- minutes - результат форматирования в минутах;
- hours - результат форматирования в часах;
- days - результат форматирования в днях.

Если аргумент спецификатора не указан, форматирование осуществляется в наиболее подходящей размерности.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{|time}}', [60 * 60]); // 1 ч.
Formatter::format('{{|time("minutes")}}', [60 * 60]); // 60 мин.
Formatter::format('{{|time}}', [2 * 24 * 60 * 60]); // 2 д.
```

## PHP склонение слов после числительных

С помощью функции спецификатора `declension` можно склонять существительные после чисел.
Например: 1 год, 2 года, 5 лет.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 1]); // 1 год
Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 2]); // 2 года
Formatter::format('{{year}} {{year|declension("год", "года", "лет")}}', ['year' => 5]); // 5 лет
```

Модификаторы функции спецификатора склонения существительного после числа:

- первый модификатор задает текст для единичных значений;
- второй для значений с 2-х до 4-х;
- третий для всех остальных.

## Форматирование телефонных номеров

С помощью функции спецификатора `phone` можно форматировать номера телефонов по заданной маске (формату):

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|phone("+7(ddd)ddd-dddd")}}', ['value' => '+79228223576']); // +7(922)822-3576
Formatter::format('{{value|phone("+7(ddd)ddd-dddd")}}', ['value' => '9228223576']); // +7(922)822-3576
Formatter::format('{{value|phone("(dddd)dd-dd-dd")}}', ['value' => '(6783)44-00-44']); // (6783)44-00-44
```

## Форматирование числа

С помощью функции спецификатора `number` можно форматировать числа:

Модификаторы функции спецификатора форматирования числа:

- int decimals (2) - число знаков после запятой;
- string decimalSeparator ('.') - разделитель дробной части;
- string thousandsSeparator ('') - разделитель тысяч;
- bool allowZeroDecimal (false) - разрешить вывод 0 в конце дробной части.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|number}}', ['value' => 100.00]); // 100

Formatter::format(
    '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
    ['value' => 100100.00],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => true,
    ]
); // 100 100.00

Formatter::format(
    '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
    ['value' => 100100.00],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
    ]
); // 100 100

Formatter::format(
    '{{value|number(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}}',
    ['value' => 100100.12],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
    ]
); // 100 100.12
```

Для упрощения ввода функций спецификаторов форматирования числа, воспользуйтесь сокращением.

```php
use Fi1a\Format\Formatter;

Formatter::addShortcut('number', 'number(2, ".", " ", false)');
Formatter::format('{{value|~number}}', ['value' => 100100.12]); // 100 100.12
Formatter::format('{{value|~number}}', ['value' => 100100]); // 100 100
```

## PHP форматирование цены

С помощью функции спецификатора `price` можно форматировать цены:

Модификаторы функции спецификатора форматирования цены:

- int decimals (2) - число знаков после запятой;
- string decimalSeparator ('.') - разделитель дробной части;
- string thousandsSeparator ('') - разделитель тысяч;
- bool allowZeroDecimal (false) - разрешить вывод 0 в конце дробной части.
- int round (null) - округляет цену. Передавать  константы: PHP_ROUND_HALF_UP - округляет от нуля, когда следующий знак находится посередине, PHP_ROUND_HALF_DOWN - округляет к нулю, когда следующий знак находится посередине.  
- int roundPrecision (0) - количество десятичных знаков, до которых производится округление.
- bool floor (false) - округляет в меньшую сторону (до целового).

```php
use Fi1a\Format\Formatter;

Formatter::format('{{value|price}} руб.', ['value' => 100.00]); // 100 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}} руб.',
    ['value' => 100100.00],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => true,
    ]
); // 100 100.00 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}} руб.',
    ['value' => 100100.00],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
    ]
); // 100 100 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal)}} руб.',
    ['value' => 100100.12],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
    ]
); // 100 100.12 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal, round, roundPrecision)}} руб.',
    ['value' => 100100.5],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
        'round' => PHP_ROUND_HALF_UP,
        'roundPrecision' => 0,
    ]
); // 100 101 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal, round, roundPrecision)}} руб.',
    ['value' => 100100.5],
    [
        'decimals' => 2,
        'decimalSeparator' => '.',
        'thousandsSeparator' => ' ',
        'allowZeroDecimal' => false,
        'round' => PHP_ROUND_HALF_DOWN,
        'roundPrecision' => 0,
    ]
); // 100 100 руб.

Formatter::format(
    '{{value|price(decimals, decimalSeparator, thousandsSeparator, allowZeroDecimal, round, roundPrecision, floor)}} руб.',
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
); // 100 100 руб.
```

Для упрощения ввода функций спецификаторов форматирования цены, воспользуйтесь сокращением.

```php
use Fi1a\Format\Formatter;

Formatter::addShortcut('price', 'price(0, ".", " ", false, null, 0, true)');
Formatter::format('{{value|~price}} руб.', ['value' => 100100.6]); // 100 100 руб.
```

## Условные конструкции

Доступны следующий условные конструкции: ```if, elseif, else, endif```.
При отсутствии ключа в массиве значений исключение не выбрасывается.

```php
use Fi1a\Format\Formatter;

Formatter::format('{{if(foo)}}{{bar}}{{else}}false{{endif}}', ['foo' => true, 'bar' => 'bar']); // bar

Formatter::format('{{if(not_exists)}}{{foo}}{{elseif(bar)}}{{bar}}{{endif}}', ['foo' => 'foo', 'bar' => 'bar']); // bar
```

Возможно использование функций спецификаторов в условных конструкциях:

```php
use Fi1a\Format\Formatter;

Formatter::format('{{if(value|unescape==="a&b")}}{{value}}{{endif}}', ['value' => 'a&b']); // "a&amp;b"
```

## Добавление функций спецификаторов

Класс функции спецификатора должен реализовывать интерфейс ```\Fi1a\Format\Specifier\ISpecifier```

Добавление новой функции спецификатора осуществляется с помощью метода ```Fi1a\Format\Formatter::addSpecifier()```:

```php
use Fi1a\Format\Formatter;

Formatter::addSpecifier('spf', Specifier::class); // true

Formatter::format('{{foo|spf(true)}}', ['foo' => 'foo']); // foo
```

## Сокращения

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
[badge-coverage]: https://img.shields.io/badge/coverage-100%25-green
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/format.svg?style=flat-square&colorB=mediumvioletred
[badge-mail]: https://img.shields.io/badge/mail-support%40fi1a.ru-brightgreen

[packagist]: https://packagist.org/packages/fi1a/format
[license]: https://github.com/fi1a/format/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/fi1a/format
[mail]: mailto:support@fi1a.ru