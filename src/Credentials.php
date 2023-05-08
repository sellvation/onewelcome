<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Carbon\Carbon;
use JsonException;

class Credentials
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $idToken;

    /**
     * @var string
     */
    private $tokenType;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var int
     */
    private $expiresAt;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromOneWelcomeAPIResponse(array $credentials): self
    {
        Assertion::keyExists($credentials, 'access_token');
        Assertion::keyExists($credentials, 'refresh_token');
        Assertion::keyExists($credentials, 'scope');
        Assertion::keyExists($credentials, 'id_token');
        Assertion::keyExists($credentials, 'token_type');
        Assertion::keyExists($credentials, 'expires_in');

        $instance = new self();
        $instance->accessToken = $credentials['access_token'];
        $instance->refreshToken = $credentials['refresh_token'];
        $instance->scope = $credentials['scope'];
        $instance->idToken = $credentials['id_token'];
        $instance->tokenType = $credentials['token_type'];
        $instance->expiresIn = (int) $credentials['expires_in'];
        $instance->expiresAt = Carbon::now()->addSeconds($instance->expiresIn)->timestamp;

        return $instance;
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'accessToken');
        Assertion::keyExists($data, 'refreshToken');
        Assertion::keyExists($data, 'scope');
        Assertion::keyExists($data, 'idToken');
        Assertion::keyExists($data, 'tokenType');
        Assertion::keyExists($data, 'expiresIn');
        Assertion::keyExists($data, 'expiresAt');

        $instance = new self();
        $instance->accessToken = $data['accessToken'];
        $instance->refreshToken = $data['refreshToken'];
        $instance->scope = $data['scope'];
        $instance->idToken = $data['idToken'];
        $instance->tokenType = $data['tokenType'];
        $instance->expiresIn = (int) $data['expiresIn'];
        $instance->expiresAt = (int) $data['expiresAt'];

        return $instance;
    }

    /**
     * @phpstan-ignore-next-line
     * @throws JsonException|AssertionFailedException
     */
    public static function fromJson(string $jsonData): self
    {
        return self::fromArray(json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR));
    }

    public function toArray(): array
    {
        return [
            'accessToken' => $this->getAccessToken(),
            'refreshToken' => $this->getRefreshToken(),
            'scope' => $this->getScope(),
            'idToken' => $this->getIdToken(),
            'tokenType' => $this->getTokenType(),
            'expiresIn' => $this->getExpiresIn(),
            'expiresAt' => $this->getExpiresAt(),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getIdToken(): string
    {
        return $this->idToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Returns the amount of time in seconds before the token expires
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Returns a unix timestamp when the token expires
     */
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }
}
