<?php
declare(strict_types=1);

namespace unit\Scim;

use Assert\AssertionFailedException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Credentials;
use Sellvation\OneWelcome\Exceptions\APIException;
use JsonException;
use Sellvation\OneWelcome\Scim\ScimClient;

class ScimClientTest extends TestCase
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
     * @var ScimClient
     */
    private $scimAPI;

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

        $this->scimAPI = new ScimClient($client, $credentials);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnCorrectValuesWhenCreatingNewUserObject(): void
    {
        $responseJson = '{"meta":{"created":"2022-03-09T12:40:51Z","lastModified":"2022-03-16T14:05:39Z","version":"W/-1138364473","location":"https://domain.com/name/scim/v1/Users/11e7188b-95fd-4d79-a14a-28e3966832c5"},"name":{"givenName":"OtherFirstname","formatted":"OtherFirstname"},"emails":[{"type":"home","value":"email@domain.com","primary":true}],"id":"61e7688b-95fd-4d79-a14a-28e3966832c5","schemas":["urn:scim:schemas:core:1.0","urn:scim:schemas:extension:iwelcome:1.0","urn:scim:schemas:extension:iwelcome:1.0:ritm","urn:scim:schemas:extension:iwelcomeattributemetadata:1.0","urn:scim:schemas:extension:iwelcomeattributevaluemetadata:1.0"],"userName":"email@domain.com","active":true,"preferredLanguage":"nl_NL","urn:scim:schemas:extension:iwelcomeattributemetadata:1.0":{"metadata":[{"extensionName":"urn:scim:schemas:core:1.0","attributeName":"name","createDate":"2022-03-16T14:05:39Z"},{"extensionName":"urn:scim:schemas:core:1.0","attributeName":"active","createDate":"2022-03-16T14:05:39Z"},{"extensionName":"urn:scim:schemas:extension:iwelcome:1.0:ritm","attributeName":"groups","createDate":"2022-03-09T12:40:52Z","changeDate":"2022-03-16T14:05:39Z"}]},"urn:scim:schemas:extension:iwelcome:1.0:ritm":{"groups":[{"structureCode":"structure-gcqdh4tUWghp","itemCode":"61e7618b-95fd-4d79-a14a-28e3266832c5"}]},"urn:scim:schemas:extension:iwelcome:1.0":{"segment":"name","lastSuccessfulLogin":"2022-03-09T13:06:47Z","state":"ACTIVE"},"urn:scim:schemas:extension:iwelcomeattributevaluemetadata:1.0":{"attributevaluemetadata":[{"userId":"11e7588b-95fd-4d79-a14a-18e9966852c5","attrName":"urn:scim:schemas:core:1.0:emails","attrId":"b1e33b6422f24bf1b679f48e64fd0a64","verifierPerson":"None","verificationLevel":1,"validity":"Not validated","verificationCorrelationId":"Not verified","lastRefresh":"2022-03-09T12:40:51.207Z","classification":"Personal data","releasability":"None"},{"userId":"11e7689b-95fd-4d79-a13a-29e3266832c5","attrName":"urn:scim:schemas:core:1.0:name:givenName","provider":"iWelcome","verifier":"Not verified","verifierPerson":"None","verificationLevel":0,"validity":"Not validated","lastRefresh":"2022-03-16T14:05:39.508Z","classification":"Personal data"}]}}';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $userArray = $responseArray;
        $userObject = $this->scimAPI->getUserById('123456789');

        $this->assertEquals($userArray['id'], $userObject->getId());
        $this->assertEquals($userArray['name']['givenName'], $userObject->getName());
        $this->assertEquals($userArray['meta']['created'], $userObject->getCreated()->format('Y-m-d\TH:i:s\Z'));
        $this->assertEquals($userArray['meta']['lastModified'], $userObject->getLastModified()->format('Y-m-d\TH:i:s\Z'));
        $this->assertEquals($userArray['urn:scim:schemas:extension:iwelcome:1.0']['state'], $userObject->getState());
        $this->assertEquals((bool) $userArray['active'], $userObject->isActive());

        $this->assertCount(1, $userObject->getEmails());
        $this->assertEquals($userArray['emails'][0]['value'], $userObject->getEmails()->current()->getValue());
        $this->assertEquals((bool) $userArray['emails'][0]['primary'], $userObject->getEmails()->current()->isPrimary());
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldThrowExceptionWhenNonExistingUserIdIsProvided(): void
    {
        $responseJson = '{"field":"id","message":"User validation error","code":404,"Errors":[{"field":"id","message":"User with id: non-existing-user-id does not exist","code":404}]}"';

        $this->mockResponse->method('getStatusCode')->willReturn(404);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $this->expectException(APIException::class);
        $this->scimAPI->getUserById('non-existing-user-id');
    }
}
