<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Scim;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\AbstractClient;
use Sellvation\OneWelcome\Exceptions\APIException;

class ScimClient extends AbstractClient
{
    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function getUserById(string $userId): User
    {
        $response = $this->request('get', sprintf(getenv('SCIM_URL'), $userId));
        return User::fromArray($response);
    }
}
