<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Config;

interface APIConfigInterface
{
    public function getAuthenticationURL(): string;

    public function getClientId(): string;

    public function getClientSecret(): string;

    public function getGrantType(): string;

    public function getScope(): string;

    public function getUsername(): string;

    public function getPassword(): string;

    public function setGrantType(string $grantType): void;
}
