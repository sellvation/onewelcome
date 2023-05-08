<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Scim;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Carbon\Carbon;
use Sellvation\OneWelcome\Scim\Collections\EmailCollection;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $state;

    /**
     * @var Carbon
     */
    private $lastModified;

    /**
     * @var Carbon
     */
    private $created;

    /**
     * @var EmailCollection
     */
    private $emailCollection;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'id');
        Assertion::keyExists($data, 'meta');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'emails');
        Assertion::keyExists($data, 'active');
        Assertion::keyExists($data, 'urn:scim:schemas:extension:iwelcome:1.0');
        Assertion::isArray($data, 'urn:scim:schemas:extension:iwelcome:1.0');
        Assertion::keyExists($data['urn:scim:schemas:extension:iwelcome:1.0'], 'state');
        Assertion::isArray($data, 'emails');

        $instance = new self();
        $instance->id = $data['id'];
        $instance->name = $data['name']['givenName'];
        $instance->created = Carbon::parse($data['meta']['created']);
        $instance->lastModified = Carbon::parse($data['meta']['lastModified']);
        $instance->active = (bool) $data['active'];
        $instance->state = $data['urn:scim:schemas:extension:iwelcome:1.0']['state'];
        $instance->emailCollection = EmailCollection::fromArray($data['emails']);

        return $instance;
    }

    public function getCreated(): Carbon
    {
        return $this->created;
    }

    public function getLastModified(): Carbon
    {
        return $this->lastModified;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmails(): EmailCollection
    {
        return $this->emailCollection;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPrimaryEmailAddress(): ?Email
    {
        foreach ($this->emailCollection as $email) {
            if (false === $email->isPrimary()) {
                continue;
            }

            return $email;
        }

        return null;
    }
}
