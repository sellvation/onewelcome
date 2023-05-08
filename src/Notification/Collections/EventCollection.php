<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Notification\Collections;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\Notification\Event;

class EventCollection extends AbstractCollection
{
    public function __construct(Event ...$events)
    {
        parent::__construct($events);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $events): self
    {
        $instance = new self();

        foreach ($events as $event) {
            $instance->append(Event::fromResponseArray($event));
        }

        return $instance;
    }

    public function current(): ?Event
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?Event
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(Event $event): void
    {
        $this->items[] = $event;
    }
}
