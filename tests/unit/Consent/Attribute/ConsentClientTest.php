<?php
declare(strict_types=1);

namespace unit\Consent\Attribute;

use Assert\AssertionFailedException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use JsonException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Consent\Attribute\ConsentClient;
use Sellvation\OneWelcome\Credentials;
use Sellvation\OneWelcome\Exceptions\APIException;

class ConsentClientTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mockResponse;

    /**
     * @var MockObject
     */
    private $mockStream;

    /**
     * @var ConsentClient
     */
    private $consentAPI;

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    protected function setUp(): void
    {
        $this->mockResponse = $this->createMock(Response::class);
        $this->mockStream = $this->createMock(Stream::class);
        $mockClient = $this->createMock(GuzzleClient::class);

        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $mockClient->method('request')->willReturn($this->mockResponse);

        $client = new APIClient($mockClient);

        $credentials = Credentials::fromOneWelcomeAPIResponse([
            'access_token' => '123',
            'refresh_token' => '456',
            'scope' => 'scope',
            'id_token' => '789',
            'token_type' => 'foo',
            'expires_in' => 2000,
        ]);

        $this->consentAPI = new ConsentClient($client, $credentials);
    }

    /**
     * @throws APIException
     */
    public function testShouldDeleteConsentWhenProvidingConsentId(): void
    {
        $this->mockResponse->method('getStatusCode')->willReturn(204);
        $this->mockStream->method('__toString')->willReturn('');

        $this->consentAPI->deleteConsentById('6315f902c1e7f6411d12cb11');
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnCorrectCollectionWhenRetrievingAllConsentsByUserId(): void
    {
        $responseJson = '[{"id":"2305d76fc247f641dd17cb58","userId":"1775c492-17c3-434c-b7a7-c25d11ee4732","processingPurposeId":"offers","attribute":{"name":"offers"},"consent":{"dateConsented":"2022-08-24T09:46:55.121+02:00","grantorUser":"1775c492-17c3-434c-b7a7-c25d11ee4732","locale":"en_GB"}}]';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $consentCollection = $this->consentAPI->getConsentByUserId('1775c492-17c3-434c-b7a7-c25d11ee4732');
        $consentObject = $consentCollection->current();
        $consentArray = $responseArray[0];

        $this->assertCount(1, $consentCollection);
        $this->assertSame($consentArray['id'], $consentObject->getId());
        $this->assertSame($consentArray['userId'], $consentObject->getUserId());
        $this->assertSame($consentArray['processingPurposeId'], $consentObject->getProcessingPurposeId());
        $this->assertSame($consentArray['attribute']['name'], $consentObject->getAttribute()->getName());
        $this->assertSame($consentArray['consent']['dateConsented'], $consentObject->getConsent()->getDateConsented());
        $this->assertSame($consentArray['consent']['grantorUser'], $consentObject->getConsent()->getGrantorUser());
        $this->assertSame($consentArray['consent']['locale'], $consentObject->getConsent()->getLocale());
    }

    /**
     * @throws APIException|AssertionFailedException
     */
    public function testShouldTriggerExceptionWhenRetrievingConsentsByNonExistingUserId(): void
    {
        $this->expectException(APIException::class);
        $responseJson = '[{"error_code":"ACT000002","message":"Operation failed.","description":"Cannot find attribute consent with the specified user id"}]';

        $this->mockResponse->method('getStatusCode')->willReturn(400);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $this->consentAPI->getConsentByUserId('non-existing-user-id');
    }

    /**
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnNewConsentWhenCreatingConsentForUserId(): void
    {
        $responseJson = '{"id":"630a2ae0ccea420c32db28d2","userId":"2176c492-d723-484c-b7a7-c92d10ee4731","processingPurposeId":"wine","attribute":{"name":"wine"},"consent":{"dateConsented":"2022-08-25T09:55:12.711+02:00","locale":"nl_NL"}}';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $consentObject = $this->consentAPI->createConsent('2176c492-d723-484c-b7a7-c92d10ee4731', 'wine');
        $consentArray = $responseArray;

        $this->assertSame($consentArray['id'], $consentObject->getId());
        $this->assertSame($consentArray['userId'], $consentObject->getUserId());
        $this->assertSame($consentArray['processingPurposeId'], $consentObject->getProcessingPurposeId());
        $this->assertSame($consentArray['attribute']['name'], $consentObject->getAttribute()->getName());
        $this->assertSame($consentArray['consent']['dateConsented'], $consentObject->getConsent()->getDateConsented());
        $this->assertSame($consentArray['consent']['grantorUser'], $consentObject->getConsent()->getGrantorUser());
        $this->assertSame($consentArray['consent']['locale'], $consentObject->getConsent()->getLocale());
    }


    /**
     * @throws APIException|AssertionFailedException
     */
    public function testShouldTriggerExceptionWhenCreatingConsentsForNonExistingUserId(): void
    {
        $this->expectException(APIException::class);
        $responseJson = '[{"error_code":"ACT000001","message":"Could not create consent for user.","description":"Could not create consent because the user does not exist."}]';

        $this->mockResponse->method('getStatusCode')->willReturn(400);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $this->consentAPI->createConsent('non-existing-user-id', 'wine');
    }
}