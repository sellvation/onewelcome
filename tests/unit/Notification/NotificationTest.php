<?php
declare(strict_types=1);

namespace unit\Notification;

use Assert\AssertionFailedException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\APIClient;
use Sellvation\OneWelcome\Credentials;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\Notification\NotificationClient;
use JsonException;

class NotificationTest extends TestCase
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
     * @var NotificationClient
     */
    private $notificationAPI;

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

        $this->notificationAPI = new NotificationClient($client, $credentials);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function testShouldReturnCorrectValuesWhenCreatingNewNotificationObject(): void
    {
        $responseJson = '{"page":1,"size":100,"results":[{"version":"V2","event_type_id":"301","event_type_details":{"category":"IDENTITY_LIFECYCLE","name":"create account"},"timestamp":"2022-05-20T06:36:35.078584Z","user":{"id":"a0a6fcd0-0beb-479c-9af3-20575ba512ba","segment":"segment"},"client_application":{},"device":{"os":"Other","user_agent":"python-requests/2.27.1","browser":"Python Requests","browser_version":"2.27"},"location":{"ip":"1.1.1.1"},"authenticated_user":{"id":"login-api"},"identity_status_transition":{"new_state":"INACTIVE"}}]}';
        $responseArray = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $notificationArray = $responseArray;
        $notificationObject = $this->notificationAPI->getNotificationBySubscriptionId('123456789');
        $eventsArray = $responseArray['results'][0];

        $eventsObject = $notificationObject->getEventCollection();
        $event = $eventsObject->current();

        $this->assertEquals($notificationArray['page'], $notificationObject->getPage());
        $this->assertEquals($notificationArray['size'], $notificationObject->getSize());
        $this->assertCount(1, $eventsObject);

        $this->assertEquals($eventsArray['version'], $event->getVersion());
        $this->assertEquals($eventsArray['identity_status_transition']['new_state'], $event->getState());
        $this->assertEquals($eventsArray['event_type_id'], $event->getTypeId());
        $this->assertEquals($eventsArray['event_type_details']['category'], $event->getCategory());
        $this->assertEquals($eventsArray['event_type_details']['name'], $event->getName());
        $this->assertEquals($eventsArray['timestamp'], $event->getTimestamp()->format('Y-m-d\TH:i:s\.u\Z'));
        $this->assertEquals($eventsArray['identity_status_transition']['new_state'], $event->getState());
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldThrowExceptionWhenNonExistingSubscriptionIdIsProvided(): void
    {
        $responseJson = '{"error_code":"NOT000006","message":"Subscription not found","description":"Subscription with id [62465a671bcce771843252c1c6] for client_id [user] does not exist."}"';

        $this->mockResponse->method('getStatusCode')->willReturn(404);
        $this->mockStream->method('__toString')->willReturn($responseJson);

        $this->expectException(APIException::class);
        $this->notificationAPI->getNotificationBySubscriptionId('non-existing-user-id');
    }
}
