<?php

declare(strict_types=1);

use Fi1a\Format\Formatter;
use Fi1a\Format\Specifier\Sprintf;

Formatter::addSpecifier('sprintf', Sprintf::class);
