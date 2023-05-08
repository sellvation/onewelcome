<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Consent\Attribute;

use Assert\Assertion;
use Assert\AssertionFailedException;

class Attribute
{
    /**
     * @var string
     */
    private $name;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $attribute): self
    {
        Assertion::keyExists($attribute, 'name');

        $instance = new self();
        $instance->name = (string) $attribute['name'];

        return $instance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }
}
