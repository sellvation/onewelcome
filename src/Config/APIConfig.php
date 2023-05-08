<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Config;

class APIConfig implements APIConfigInterface
{
    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $authenticationURL;

    /**
     * @var string
     */
    private $grantType = 'password';

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $authenticationURL, string $clientId, string $clientSecret, string $scope, string $username, string $password)
    {
        $this->clientSecret = $clientSecret;
        $this->clientId = $clientId;
        $this->authenticationURL = $authenticationURL;
        $this->scope = $scope;
        $this->username = $username;
        $this->password = $password;
    }

    public function getAuthenticationURL(): string
    {
        return $this->authenticationURL;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function setGrantType(string $grantType): void
    {
        $this->grantType = $grantType;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
