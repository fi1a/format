<?php

declare(strict_types=1);

namespace Fi1a\Unit\Format\Fixtures;

/**
 * Класс для тестирования
 */
class FormatClass
{
    /**
     * @var string
     */
    public $fix1 = 'string';

    /**
     * @var int
     */
    public $fix2 = 20;

    /**
     * @var array
     */
    public $fix3 = ['fix4' => ['fix5' => 5],];

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return 'FIX';
    }
}
