<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\Collections;

use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\RITM\ValueObjects\Address;

class AddressCollection extends AbstractCollection
{
    public function __construct(Address ...$addresses)
    {
        parent::__construct($addresses);
    }

    public static function fromArray(array $addresses): self
    {
        $instance = new self();

        foreach ($addresses as $address) {
            $instance->append(Address::fromArray($address));
        }

        return $instance;
    }

    public function current(): ?Address
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?Address
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(Address $address): void
    {
        $this->items[] = $address;
    }

    public function toOneWelcomeFormat(): array
    {
        $output = [];

        /** @var Address $address */
        foreach ($this->items as $address) {
            $output[] = $address->toOneWelcomeFormat();
        }

        return $output;
    }
}
