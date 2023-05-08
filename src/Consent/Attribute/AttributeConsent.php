<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Consent\Attribute;

use Assert\Assertion;
use Assert\AssertionFailedException;
use JsonException;

class AttributeConsent
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $processingPurposeId;

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var Consent
     */
    private $consent;

    private function __construct()
    {
    }

    /**
     * Returns a unique fingerprint for the current data in the User object.
     * When user data changes, the fingerprint will change as well.
     * @throws JsonException
     */
    public function getFingerprint(): string
    {
        return hash('ripemd160', $this->toJson());
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $consent): self
    {
        Assertion::keyExists($consent, 'userId');
        Assertion::keyExists($consent, 'processingPurposeId');
        Assertion::keyExists($consent, 'attribute');
        Assertion::isArray($consent['attribute']);
        Assertion::keyExists($consent, 'consent');
        Assertion::isArray($consent['consent']);

        $instance = new self();
        $instance->id = $consent['id'] ?? null;
        $instance->userId = (string) $consent['userId'];
        $instance->processingPurposeId = (string) $consent['processingPurposeId'];
        $instance->attribute = Attribute::fromArray($consent['attribute']);
        $instance->consent = Consent::fromArray($consent['consent']);

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'processingPurposeId' => $this->getProcessingPurposeId(),
            'attributes' => $this->getAttribute(),
            'consent' => $this->getConsent(),
        ];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getProcessingPurposeId(): string
    {
        return $this->processingPurposeId;
    }

    public function getConsent(): Consent
    {
        return $this->consent;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }
}
