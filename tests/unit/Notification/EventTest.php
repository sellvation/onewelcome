<?php
declare(strict_types=1);

namespace unit\Notification;

use Assert\AssertionFailedException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\Notification\Event;

class EventTest extends TestCase
{
    /**
     * @throws AssertionFailedException
     */
    public function testShouldCreateValidEventObjectWhenCreatingFromArray(): void
    {
        $eventArray = [
            'version' => 'V2',
            'typeId' => '1',
            'category' => '1',
            'name' => '1',
            'userId' => '1',
            'timestamp' => 1650534735,
            'state' => 'GRACE',
        ];

        $eventObject = Event::fromArray($eventArray);
        $eventArray = $eventObject->toArray();
        $eventObject = Event::fromArray($eventArray);

        $this->assertEquals($eventObject->getVersion(), $eventArray['version']);
        $this->assertEquals($eventObject->getTypeId(), $eventArray['typeId']);
        $this->assertEquals($eventObject->getCategory(), $eventArray['category']);
        $this->assertEquals($eventObject->getName(), $eventArray['name']);
        $this->assertEquals($eventObject->getUserId(), $eventArray['userId']);
        $this->assertEquals($eventObject->getState(), $eventArray['state']);
        $this->assertEquals($eventObject->getTimestamp()->getTimestamp(), $eventArray['timestamp']);
    }

    /**
     * @throws AssertionFailedException|JsonException
     */
    public function testShouldCreateValidEventObjectWhenCreatingFromJson(): void
    {
        $eventJson = '{"version":"V2","typeId":"1","category":"1","name":"1","userId":"1","timestamp":1650534735,"state":"GRACE"}';
        $eventArray = json_decode($eventJson, true, 512, JSON_THROW_ON_ERROR);
        $eventObject = Event::fromJson($eventJson);

        $this->assertEquals($eventObject->getVersion(), $eventArray['version']);
        $this->assertEquals($eventObject->getTypeId(), $eventArray['typeId']);
        $this->assertEquals($eventObject->getCategory(), $eventArray['category']);
        $this->assertEquals($eventObject->getName(), $eventArray['name']);
        $this->assertEquals($eventObject->getUserId(), $eventArray['userId']);
        $this->assertEquals($eventObject->getState(), $eventArray['state']);
        $this->assertEquals($eventObject->getTimestamp()->getTimestamp(), $eventArray['timestamp']);
        $this->assertEquals($eventObject->toJson(), $eventJson);
    }

    public function testShouldThrowExceptionWhenNonExistingStateIsProvided(): void
    {
        $eventArray = [
            'version' => 'V2',
            'typeId' => '1',
            'category' => '1',
            'name' => '1',
            'userId' => '1',
            'timestamp' => 1650534735,
            'state' => 'NON-EXISTING-STATE',
        ];

        $this->expectException(AssertionFailedException::class);
        Event::fromArray($eventArray);
    }
}