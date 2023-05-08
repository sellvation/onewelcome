<?php
declare(strict_types=1);

namespace unit\RITM;

use Assert\AssertionFailedException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Credentials;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\RITM\RITMClient;
use JsonException;

class RITMClientTest extends TestCase
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
     * @var RITMClient
     */
    private $ritmAPI;

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

        $this->ritmAPI = new RITMClient($client, $credentials);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnCorrectValuesWhenCreatingNewUserObject(): void
    {
        $responseJson = '{"totalItems":1,"limit":10,"page":1,"pageCount":1,"result":[{"profileInformation":{"uid":"5ba495f1-15c6-4e33-aeb0-1d8dc6eeb954","emails":[{"value":"mail@domain.com","type":"other","primary":true}],"phoneNumbers":[{"value":"+31612345678","type":"other","primary":true}],"urn:scim:schemas:extension:iwelcome:1.0":{"lastSuccessfulLogin":"2023-05-03T13:20:34Z","state":"ACTIVE","birthDate":"2000-01-01"},"urn:scim:schemas:extension:plus:1.0":{"IsB2C":true,"DeliveryAddresses":[{"value":"address1","Street":"straat","HouseNumber":"1","HouseNumberAddition":"A","PostalCode":"6500AA","City":"Arnhem","Country":"Nederland","CountryISO2":"NL","IsPreferred":true,"type":"other"}],"IsB2B":false,"Payment":{},"PreferencesOrder":{"AlternateProduct":"NO","MyStore":"123"},"FirstName":"John","PrivacyPolicyConsentDate":"2023-05-03T08:01:11.430Z","CustomerID":1234567,"SurName":"Smith","HasAgreedPLUSPrivacyPolicy":true}},"roleAssignments":{"adminRoles":[],"personalRoles":[],"accessRoles":[{"name":"OWNER","code":"role-YJqK123PJIIN","startDate":"1970-01-01T00:00:00.000Z","endDate":"2100-01-01T00:00:00.000Z","assignedStructureCode":"structure-tm12380IAKEk","assignedStructureGroup":"g-4002123","applications":[],"resources":[]}]},"structureMemberships":[{"code":"structure-tmtp123IAKEk","groupMemberships":[{"code":"group-iDlWI8O123N1","name":"group-iDlWI8O123N1"},{"code":"g-4002123","name":"g-4002123"}],"name":"Foo"}]}]}';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $userArray = $responseArray;
        $userObject = $this->ritmAPI->getUserByUUID('123456789');

        $profileInformation = $userArray['result'][0]['profileInformation'];
        $name = $profileInformation[getenv('RITM_CUSTOMER_TAG')];

        $this->assertEquals($profileInformation['uid'], $userObject->getUUID());
        $this->assertEquals($profileInformation[getenv('RITM_CUSTOMER_TAG')][getenv('RITM_CUSTOMER_KEY')], $userObject->getCustomerId());
        $this->assertEquals($name['FirstName'], $userObject->getFirstName());
        $this->assertEquals($name['SurName'], $userObject->getLastName());
        $this->assertEquals($profileInformation['urn:scim:schemas:extension:iwelcome:1.0']['state'], $userObject->getState());

        $this->assertCount(1, $userObject->getEmails());
        $this->assertEquals($profileInformation['emails'][0]['value'], $userObject->getEmails()->current()->getValue());
        $this->assertEquals($profileInformation['emails'][0]['type'], $userObject->getEmails()->current()->getType());
    }
}
