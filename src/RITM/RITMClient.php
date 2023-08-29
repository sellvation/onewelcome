<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\RITM;

use Assert\Assertion;
use Assert\AssertionFailedException;
use JsonException;
use Sellvation\OneWelcome\AbstractClient;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\Notification\Event;
use Sellvation\OneWelcome\RITM\ValueObjects\User;

class RITMClient extends AbstractClient
{
    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function getUserByUUID(string $userUUID): ?User
    {
        $response = $this->request('get', sprintf(getenv('RITM_URL'), $userUUID));

        Assertion::keyExists($response, 'result');
        Assertion::isArray($response, 'result');

        if (0 === count($response['result'])) {
            return null;
        }

        return User::fromArray($response['result'][0]);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|JsonException|AssertionFailedException
     */
    public function saveUser(User $user): User
    {
        $response = $this->request(
            'patch',
            getenv('RITM_SAVE_URL'),
            ['Content-Type' => 'application/json'],
            json_encode([
                'uid' => $user->getUUID(),
                $user->toOneWelcomeFormat()
            ], JSON_THROW_ON_ERROR)
        );

        return User::fromArray($response['result'][0]);
    }

    /**
     * @throws APIException|JsonException
     */
    public function saveStateForUserUUID(string $uuid, string $state, string $dateTimestampString): bool
    {
        Event::validateState($state);

        $response = $this->request(
            'patch',
            sprintf(getenv('RITM_SAVE_URL'), $uuid),
            ['Content-Type' => 'application/json'],
            json_encode([
                'profileInformation' => [
                    'urn:scim:schemas:extension:iwelcome:1.0' => [
                        'state' => $state
                    ],
                    'urn:scim:schemas:extension:plus:1.0' => [
                        'LastActivityDate' => $dateTimestampString
                    ]
                ]
            ], JSON_THROW_ON_ERROR)
        );

        return (bool) ($response['urn:scim:schemas:extension:iwelcome:1.0']['state'] ?? false);
    }
}
