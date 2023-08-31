<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\Collections;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\RITM\ValueObjects\Email;

class EmailCollection extends AbstractCollection
{
    public function __construct(Email ...$emails)
    {
        parent::__construct($emails);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $emails): self
    {
        $instance = new self();

        foreach ($emails as $email) {
            $instance->append(Email::fromArray($email));
        }

        return $instance;
    }

    public function current(): ?Email
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function first(): ?Email
    {
        return ($item = reset($this->items)) ? $item : null;
    }

    public function next(): ?Email
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(Email $email): void
    {
        $this->items[] = $email;
    }

    public function filterType(string $type): self
    {
        $instance = new self();

        foreach ($this->items as $email) {
            if ($type !== $email->getType()) {
                continue;
            }

            $instance->append($email);
        }

        return $instance;
    }

    public function toOneWelcomeFormat(): array
    {
        $output = [];

        /** @var Email $email */
        foreach ($this->items as $email) {
            $output[] = $email->toOneWelcomeFormat();
        }

        return $output;
    }
}
