<?php
declare(strict_types=1);

namespace unit\Scim;

use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\Scim\User;

class UserTest extends TestCase
{
    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public function testShouldReturnCorrectPrimaryEmailWhenMultipleEmailsAreProvided(): void
    {
        $user = User::fromArray([
            'id' => '61e7688b-95fd-4d79-a14a-28e3966832c5',
            'meta' => [
                'created' => '2022-03-09T12:40:51Z',
                'lastModified' => '2022-03-16T14:05:39Z'
            ],
            'name' => [
                'givenName' => 'name'
            ],
            'active' => true,
            'emails' => [
                [
                    'value' => 'email1@domain.com',
                    'primary' => false
                ], [
                    'value' => 'email2@domain.com',
                    'primary' => true
                ], [
                    'value' => 'email3@domain.com',
                    'primary' => false
                ]
            ],
            'urn:scim:schemas:extension:iwelcome:1.0' => [
                'state' => 'ACTIVE'
            ]
        ]);

        $this->assertEquals('email2@domain.com', $user->getPrimaryEmailAddress()->getValue());
    }
}