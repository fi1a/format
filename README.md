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

## Использование

```php
use Fi1a\Format\Formatter;

Formatter::format('[{{user:login}}] - {{user:name}}', ['user' => ['name' => 'John', 'login' => 'john85']]); // [john85] - John

Formatter::format('{{0}}, {{1}}',[1, 2]); // 1, 2

Formatter::format('{{foo}}, {{foo}}, {{foo}}', ['foo' => 'bar',]); // bar, bar, bar
```

### Использование спецификаторов

Указание спецификатора следует после указания ключа с разделителем "|".

Пример:

```php
use Fi1a\Format\Formatter;

Formatter::format('{{0|04d}}-{{1|02d}}-{{2|02d}}',[2016, 2, 27,]); // 2016-02-27
```

Примеры и синтаксис спецификаторов доступны в [wiki проекта](https://github.com/fi1a/format/wiki).

[badge-release]: https://img.shields.io/packagist/v/fi1a/format?label=release
[badge-license]: https://img.shields.io/github/license/fi1a/format?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/fi1a/format?style=flat-square
[badge-build]: https://img.shields.io/travis/fi1a/format?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/github/fi1a/format/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/fi1a/format.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/fi1a/format
[license]: https://github.com/fi1a/format/blob/master/LICENSE
[php]: https://php.net
[build]: https://travis-ci.org/fi1a/format
[coverage]: https://coveralls.io/r/fi1a/format?branch=master
[downloads]: https://packagist.org/packages/fi1a/format
