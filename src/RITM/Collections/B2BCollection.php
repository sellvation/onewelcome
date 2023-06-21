<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\Collections;

use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\RITM\ValueObjects\B2B;

class B2BCollection extends AbstractCollection
{
    public function __construct(B2B ...$b2b)
    {
        parent::__construct($b2b);
    }

    public static function fromArray(array $b2bs): self
    {
        $instance = new self();

        foreach ($b2bs as $b2b) {
            $instance->append(B2B::fromArray($b2b));
        }

        return $instance;
    }

    public function current(): ?B2B
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?B2B
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(B2B $b2b): void
    {
        $this->items[] = $b2b;
    }

    public function toOneWelcomeFormat(): array
    {
        $output = [];

        /** @var B2B $b2b */
        foreach ($this->items as $b2b) {
            $output[] = $b2b->toOneWelcomeFormat();
        }

        return $output;
    }
}
