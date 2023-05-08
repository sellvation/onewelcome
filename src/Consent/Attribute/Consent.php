<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Consent\Attribute;

use Assert\Assertion;
use Assert\AssertionFailedException;

class Consent
{
    /**
     * @var string
     */
    private $dateConsented;

    /**
     * @var string
     */
    private $grantorUser = null;

    /**
     * @var string
     */
    private $locale;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $consent): self
    {
        Assertion::keyExists($consent, 'dateConsented');
        Assertion::keyExists($consent, 'locale');

        $instance = new self();
        $instance->dateConsented = (string) $consent['dateConsented'];
        $instance->grantorUser = $consent['grantorUser'] ?? null;
        $instance->locale = (string) $consent['locale'];

        return $instance;
    }

    public function getDateConsented(): string
    {
        return $this->dateConsented;
    }

    public function getGrantorUser(): ?string
    {
        return $this->grantorUser;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function toArray(): array
    {
        return [
            'dateConsented' => $this->getDateConsented(),
            'grantorUser' => $this->getGrantorUser(),
            'locale' => $this->getLocale(),
        ];
    }
}
