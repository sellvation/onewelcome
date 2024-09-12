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
     * @var Carbon
     */
    private $lastModified;

    /**
     * @var Carbon
     */
    private $created;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'meta');

        $instance = new self();
        $instance->created = Carbon::parse($data['meta']['created']);
        $instance->lastModified = Carbon::parse($data['meta']['lastModified']);

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
}
