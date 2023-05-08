<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Notification;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Sellvation\OneWelcome\Notification\Collections\EventCollection;

class Notification
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $size;

    /**
     * @var EventCollection
     */
    private $eventCollection;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'page');
        Assertion::keyExists($data, 'size');
        Assertion::keyExists($data, 'results');
        Assertion::isArray($data['results']);

        $instance = new self();
        $instance->page = (int) $data['page'];
        $instance->size = (int) $data['size'];
        $instance->eventCollection = EventCollection::fromArray($data['results']);

        return $instance;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getEventCollection(): EventCollection
    {
        return $this->eventCollection;
    }
}
