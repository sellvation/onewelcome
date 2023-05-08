<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Scim;

use Assert\Assertion;
use Assert\AssertionFailedException;

class Email
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $primary;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'value');
        Assertion::keyExists($data, 'primary');

        $instance = new self();
        $instance->value = $data['value'];
        $instance->primary = (bool) $data['primary'];

        return $instance;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }
}
