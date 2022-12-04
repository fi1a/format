<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Текстовая узел
 */
interface TextInterface extends NodeInterface
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
