<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Consent\Attribute\Collections;

use Assert\AssertionFailedException;
use JsonException;
use Sellvation\OneWelcome\AbstractCollection;
use Sellvation\OneWelcome\Consent\Attribute\AttributeConsent;

class ConsentCollection extends AbstractCollection
{
    public function __construct(AttributeConsent ...$consents)
    {
        parent::__construct($consents);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $consents): self
    {
        $instance = new self();

        foreach ($consents as $consent) {
            $instance->append(AttributeConsent::fromArray($consent));
        }

        return $instance;
    }

    public function current(): ?AttributeConsent
    {
        return ($item = current($this->items)) ? $item : null;
    }

    public function next(): ?AttributeConsent
    {
        return ($item = next($this->items)) ? $item : null;
    }

    public function append(AttributeConsent $consent): void
    {
        $this->items[] = $consent;
    }

    /**
     * @throws JsonException
     */
    public function getFingerprint(): string
    {
        $fingerprints = [];

        /** @var AttributeConsent $item */
        foreach ($this->items as $item) {
            $fingerprints[] = $item->getFingerprint();
        }

        return hash('ripemd160', json_encode($fingerprints, JSON_THROW_ON_ERROR));
    }
}
