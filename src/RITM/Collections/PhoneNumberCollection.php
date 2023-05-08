<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\Collections;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\RITM\ValueObjects\PhoneNumber;

class PhoneNumberCollection extends AbstractCollection
{
    public function __construct(PhoneNumber ...$phoneNumbers)
    {
        parent::__construct($phoneNumbers);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $phoneNumbers): self
    {
        $instance = new self();

        foreach ($phoneNumbers as $phoneNumber) {
            $instance->append(PhoneNumber::fromArray($phoneNumber));
        }

        return $instance;
    }

    public function current(): ?PhoneNumber
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?PhoneNumber
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(PhoneNumber $phoneNumber): void
    {
        $this->items[] = $phoneNumber;
    }

    public function filterType(string $type): self
    {
        $instance = new self();

        foreach ($this->items as $phoneNumber) {
            if ($type !== $phoneNumber->getType()) {
                continue;
            }

            $instance->append($phoneNumber);
        }

        return $instance;
    }

    public function toOneWelcomeFormat(): array
    {
        $output = [];

        /** @var PhoneNumber $phoneNumber */
        foreach ($this->items as $phoneNumber) {
            $output[] = $phoneNumber->toOneWelcomeFormat();
        }

        return $output;
    }
}
