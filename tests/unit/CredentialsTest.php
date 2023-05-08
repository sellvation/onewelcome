<?php
declare(strict_types=1);

namespace unit;

use Assert\AssertionFailedException;
use Carbon\Carbon;
use JsonException;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\Credentials;

class CredentialsTest extends TestCase
{
    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldReturnCorrectValuesWhenCreatingNewCredentialsObject(): void
    {
        $credentials = Credentials::fromOneWelcomeAPIResponse([
            'access_token' => '123',
            'refresh_token' => '456',
            'scope' => 'scope',
            'id_token' => '789',
            'token_type' => 'foo',
            'expires_in' => 2000,
        ]);

        $this->assertEquals('123', $credentials->getAccessToken());
        $this->assertEquals('456', $credentials->getRefreshToken());
        $this->assertEquals('scope', $credentials->getScope());
        $this->assertEquals('789', $credentials->getIdToken());
        $this->assertEquals('foo', $credentials->getTokenType());
        $this->assertEquals(2000, $credentials->getExpiresIn());
        $this->assertTrue(Carbon::createFromTimestamp($credentials->getExpiresAt())->between(Carbon::now()->addSeconds(2000)->subMinute(), Carbon::now()->addSeconds(2000)->addSecond()));
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException|JsonException
     */
    public function testShouldReturnAValidCredentialsObjectWhenCastingFromAndToArray(): void
    {
        $data = [
            'accessToken' => '123',
            'refreshToken' => '456',
            'scope' => 'scope',
            'idToken' => '789',
            'tokenType' => 'foo',
            'expiresIn' => 2000,
            'expiresAt' => 2000,
        ];

        $credentials = Credentials::fromArray($data);
        $this->assertEquals($data['accessToken'], $credentials->getAccessToken());
        $this->assertEquals($data['refreshToken'], $credentials->getRefreshToken());
        $this->assertEquals($data['scope'], $credentials->getScope());
        $this->assertEquals($data['idToken'], $credentials->getIdToken());
        $this->assertEquals($data['tokenType'], $credentials->getTokenType());
        $this->assertEquals($data['expiresIn'], $credentials->getExpiresIn());
        $this->assertEquals($data['expiresAt'], $credentials->getExpiresAt());

        $newData = $credentials->toArray();
        $this->assertEquals($data['accessToken'], $newData['accessToken']);
        $this->assertEquals($data['refreshToken'], $newData['refreshToken']);
        $this->assertEquals($data['scope'], $newData['scope']);
        $this->assertEquals($data['idToken'], $newData['idToken']);
        $this->assertEquals($data['tokenType'], $newData['tokenType']);
        $this->assertEquals($data['expiresIn'], $newData['expiresIn']);
        $this->assertEquals($data['expiresAt'], $newData['expiresAt']);
    }


    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException|JsonException
     */
    public function testShouldReturnAValidCredentialsObjectWhenCastingFromAndToJson(): void
    {
        $data = [
            'accessToken' => '123',
            'refreshToken' => '456',
            'scope' => 'scope',
            'idToken' => '789',
            'tokenType' => 'foo',
            'expiresIn' => 2000,
            'expiresAt' => 2000,
        ];

        $credentials = Credentials::fromJson(json_encode($data, JSON_THROW_ON_ERROR));
        $this->assertEquals($data['accessToken'], $credentials->getAccessToken());
        $this->assertEquals($data['refreshToken'], $credentials->getRefreshToken());
        $this->assertEquals($data['scope'], $credentials->getScope());
        $this->assertEquals($data['idToken'], $credentials->getIdToken());
        $this->assertEquals($data['tokenType'], $credentials->getTokenType());
        $this->assertEquals($data['expiresIn'], $credentials->getExpiresIn());
        $this->assertEquals($data['expiresAt'], $credentials->getExpiresAt());

        $newData = json_decode($credentials->toJson(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals($data['accessToken'], $newData['accessToken']);
        $this->assertEquals($data['refreshToken'], $newData['refreshToken']);
        $this->assertEquals($data['scope'], $newData['scope']);
        $this->assertEquals($data['idToken'], $newData['idToken']);
        $this->assertEquals($data['tokenType'], $newData['tokenType']);
        $this->assertEquals($data['expiresIn'], $newData['expiresIn']);
        $this->assertEquals($data['expiresAt'], $newData['expiresAt']);
    }
}
