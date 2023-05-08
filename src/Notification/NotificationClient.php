<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Notification;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractClient;
use Sellvation\OneWelcome\Exceptions\APIException;

class NotificationClient extends AbstractClient
{
    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function getNotificationBySubscriptionId(string $subscriptionId): Notification
    {
        $response = $this->request('get', sprintf(getenv('NOTIFICATION_URL'), $subscriptionId));
        return Notification::fromArray($response);
    }
}
