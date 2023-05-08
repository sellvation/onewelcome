<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM\ValueObjects;

use Assert\Assertion;
use Assert\AssertionFailedException;

class PhoneNumber
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $type;

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
        Assertion::keyExists($data, 'type');

        $instance = new self();
        $instance->value = $data['value'];
        $instance->type = $data['type'];

        return $instance;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toOneWelcomeFormat(): array
    {
        return [
            'value' => $this->getValue(),
            'type' => $this->getType()
        ];
    }
}
