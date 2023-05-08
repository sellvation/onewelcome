<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome;

use Iterator;

abstract class AbstractCollection implements Iterator
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function key()
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return null !== $this->key();
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
