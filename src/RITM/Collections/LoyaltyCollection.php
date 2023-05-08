<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\Collections;

use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\RITM\ValueObjects\Loyalty;

class LoyaltyCollection extends AbstractCollection
{
    public function __construct(Loyalty ...$loyalties)
    {
        parent::__construct($loyalties);
    }

    public static function fromArray(array $loyalties): self
    {
        $instance = new self();

        foreach ($loyalties as $loyalty) {
            $instance->append(Loyalty::fromArray($loyalty));
        }

        return $instance;
    }

    public function current(): ?Loyalty
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?Loyalty
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(Loyalty $loyalty): void
    {
        $this->items[] = $loyalty;
    }

    public function toOneWelcomeFormat(): array
    {
        $output = [];

        /** @var Loyalty $loyalty */
        foreach ($this->items as $loyalty) {
            $output[] = $loyalty->toOneWelcomeFormat();
        }

        return $output;
    }
}
