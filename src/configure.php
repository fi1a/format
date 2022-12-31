<?php

declare(strict_types=1);

use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\Date as DateSpecifier;
use Fi1a\Format\Specifier\Escape;
use Fi1a\Format\Specifier\Memory;
use Fi1a\Format\Specifier\Sprintf;
use Fi1a\Format\Specifier\Time;
use Fi1a\Format\Specifier\UnEscape;

Formatter::addSpecifier('sprintf', Sprintf::class);
Formatter::addSpecifier('date', DateSpecifier::class);
Formatter::addSpecifier('escape', Escape::class);
Formatter::addSpecifier('unescape', UnEscape::class);
Formatter::addSpecifier('memory', Memory::class);
Formatter::addSpecifier('time', Time::class);

DateSpecifier::setDayOfWeek3('ru', [
    'Mon' => 'Пн',
    'Tue' => 'Вт',
    'Wed' => 'Ср',
    'Thu' => 'Чт',
    'Fri' => 'Пт',
    'Sat' => 'Сб',
    'Sun' => 'Вс',
]);

DateSpecifier::setDayOfWeek('ru', [
    'Monday' => 'Понедельник',
    'Tuesday' => 'Вторник',
    'Wednesday' => 'Среда',
    'Thursday' => 'Четверг',
    'Friday' => 'Пятница',
    'Saturday' => 'Суббота',
    'Sunday' => 'Воскресенье',
]);

DateSpecifier::setFullMonthName('ru', [
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
]);

DateSpecifier::setFullMonthNameAccusative('ru', [
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
]);

DateSpecifier::setMonthName3('ru', [
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
]);

Memory::setDimensions([
    'B' => 'Б',
    'KB' => 'КБ',
    'MB' => 'МБ',
    'GB' => 'ГБ',
    'TB' => 'ТБ',
    'PB' => 'ПБ',
]);

Time::setTimes([
    'less_sec' => '< 1 сек.',
    'sec' => '1 сек.',
    'secs' => 'сек.',
    'min' => '1 мин.',
    'mins' => 'мин.',
    'hr' => '1 ч.',
    'hrs' => 'ч.',
    'day' => '1 д.',
    'days' => 'д.',
]);
