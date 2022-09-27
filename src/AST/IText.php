<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Текстовая узел
 */
interface IText extends INode
{
    /**
     * Конструктор
     *
     * @return void
     */
    public function __construct(string $text);

    /**
     * Возвращает текст
     */
    public function getText(): string;
}
