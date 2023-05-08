<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Scim\Collections;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\Scim\Email;

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

    public function next(): ?Email
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(Email $email): void
    {
        $this->items[] = $email;
    }
}
