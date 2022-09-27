<?php

declare(strict_types=1);

namespace Fi1a\Format\AST;

/**
 * Текстовый узел
 */
class Text implements IText
{
    /**
     * @var string
     */
    private $text;

    /**
     * @inheritDoc
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }
}
