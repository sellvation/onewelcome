<?php
declare(strict_types=1);

namespace unit;

use Assert\AssertionFailedException;
use Carbon\Carbon;
use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Config\APIConfig;
use Sellvation\OneWelcome\Exceptions\APIException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class APIClientTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mockResponse;

    /**
     * @var MockObject
     */
    private $mockClient;

    /**
     * @var MockObject
     */
    private $mockStream;

    protected function setUp(): void
    {
        $this->mockResponse = $this->createMock(Response::class);
        $this->mockClient = $this->createMock(GuzzleClient::class);
        $this->mockStream = $this->createMock(Stream::class);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnCorrectResponseWhenObtainingCredentials(): void
    {
        $responseJson = '{"access_token":"44e3b989-6826-4fcb-b2a1-97fc2ff35659","refresh_token":"ba10da41-22ba-4182-96bb-1ab25da9e957","scope":"SCIM:user:query SCIM:user:delete consent:document-consent:admin:delete openid notification:consent:consumer consent:document-consent:delete SCIM:user:post consent:document-consent:consumer SCIM:user:get SCIM:user:patch iwelcome:segment:name SCIM:user:put consent:document-consent:manager","id_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IjM3TG1KRmk1aWNoa0RLZG81MlE4Rlc4TDFkMD0ifQ.eyJhdF9oYXNoIjoiNjItVU5NdDJmY0xmZVpFVjRrOFlVQSIsInN1YiI6ImIxNTlmZTZkLTIwMjctNDBkYi1hZDViLWJlMzUxYTY1NjQ5MSIsImF1ZCI6InNhbHZhdGlvbl9hZG1pbiIsImF6cCI6InNhbHZhdGlvbl9hZG1pbiIsImF1dGhfdGltZSI6MTY0ODkyMjI4MiwiaXNzIjoiaHR0cHM6Ly9hYW5tZWxkZW4tdHN0LnBsdXMubmwvcGx1cy9hdXRoL29hdXRoMi4wL3YxIiwidG9rZW5OYW1lIjoiaWRfdG9rZW4iLCJleHAiOjE2NDg5MjU4ODIsInRva2VuVHlwZSI6IkpXVFRva2VuIiwiaWF0IjoxNjQ4OTIyMjgyLCJzZXNzaW9uLWlkIjoiNGFmYzRiOWYtZjQ3YS00MGMxLTgzNTEtZDA2NzY2ZmIxNzYwIn0.YK7mAbJbwxcXnA3-FTs_1bvJxdRsFPCwQAuP18gsrG1PiAAdasGvK9IszkM67KEbnsFnfrAtREkFJjnwgzMU6-Ndyzfyf9vrAIdjbUpA54PSlq7tcmGYrWx5oD0i2wEp7fA1UcrdthDo0AAGywagtsnpRVIlV53SsNoy_aASDuvAdPOsdXEazOEJ3JQ2e6PXh25MtfMHHUvOU_DqOPRzeYDZv4D7kBOHPCKjVUYSKyM1wACUQTQzbizaD64Vt7Le9Ko6iEvQsX3eOc8T4ZwSdegWEfx4XsgZIlsoD2ULDr-yEM_eEzLIZH0_utjTUs5Ddbjg0PYpKOcYB5ooA-Ue3Q","token_type":"Bearer","expires_in":3599}';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockClient->method('request')->willReturn($this->mockResponse);

        /** @phpstan-ignore-next-line */
        $client = new APIClient($this->mockClient);
        $config = new APIConfig('', '', '', '', '', '');
        $credentials = $client->obtainCredentials($config);

        $this->assertEquals($responseArray['access_token'], $credentials->getAccessToken());
        $this->assertEquals($responseArray['refresh_token'], $credentials->getRefreshToken());
        $this->assertEquals($responseArray['scope'], $credentials->getScope());
        $this->assertEquals($responseArray['id_token'], $credentials->getIdToken());
        $this->assertEquals($responseArray['token_type'], $credentials->getTokenType());
        $this->assertEquals($responseArray['expires_in'], $credentials->getExpiresIn());
        $this->assertTrue(Carbon::createFromTimestamp($credentials->getExpiresAt())->between(Carbon::now()->addSeconds(APIClient::TOKEN_EXPIRATION_SECONDS)->subSeconds(10), Carbon::now()->addSeconds(APIClient::TOKEN_EXPIRATION_SECONDS)->addSeconds(10)));
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldThrowExceptionWhenWrongCredentialsAreProvided(): void
    {
        $this->mockResponse->method('getStatusCode')->willReturn(400);
        $this->mockStream->method('__toString')->willReturn('Client error: `POST https://domain.com/auth/oauth2.0/v1/token` resulted in a `400 Bad Request` response: {"error":"invalid_grant","error_description":"The provided access grant is invalid, expired, or revoked."}');
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockClient->method('request')->willReturn($this->mockResponse);

        /** @phpstan-ignore-next-line */
        $client = new APIClient($this->mockClient);
        $config = new APIConfig('', '', '', '', '', '');
        $this->expectException(APIException::class);
        $client->obtainCredentials($config);
    }
}
