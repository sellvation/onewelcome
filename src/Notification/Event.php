<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Notification;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Carbon\Carbon;
use JsonException;

class Event
{
    public const STATE_GRACE = 'GRACE';
    public const STATE_INACTIVE = 'INACTIVE';
    public const STATE_ACTIVE = 'ACTIVE';
    public const STATE_WITHDRAWN = 'WITHDRAWN';
    public const STATE_BLOCKED = 'BLOCKED';

    public const STATES = [
        self::STATE_GRACE,
        self::STATE_INACTIVE,
        self::STATE_ACTIVE,
        self::STATE_WITHDRAWN,
        self::STATE_BLOCKED,
    ];

    private $version;

    /**
     * @var string
     */
    private $typeId;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Carbon
     */
    private $timestamp;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $state;

    private function __construct()
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromResponseArray(array $data): self
    {
        Assertion::keyExists($data, 'version');
        Assertion::keyExists($data, 'event_type_id');
        Assertion::keyExists($data, 'event_type_details');
        Assertion::isArray($data['event_type_details']);
        Assertion::keyExists($data, 'timestamp');
        Assertion::keyExists($data, 'user');
        Assertion::isArray($data['user']);

        $instance = new self();
        $instance->version = (string) $data['version'];
        $instance->typeId = (string) $data['event_type_id'];
        $instance->category = (string) $data['event_type_details']['category'];
        $instance->name = (string) $data['event_type_details']['name'];
        $instance->timestamp = Carbon::parse($data['timestamp']);
        $instance->userId = (string) $data['user']['id'];

        if (true === isset($data['identity_status_transition']['new_state'])) {
            $state = $data['identity_status_transition']['new_state'];
            $instance::validateState($state);
            $instance->state = $state;
        }

        return $instance;
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException
     */
    public static function fromArray(array $data): self
    {
        Assertion::keyExists($data, 'version');
        Assertion::keyExists($data, 'typeId');
        Assertion::keyExists($data, 'category');
        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'userId');
        Assertion::keyExists($data, 'timestamp');
        Assertion::between($data['timestamp'], 0, PHP_INT_MAX);

        $instance = new self();
        $instance->version = $data['version'];
        $instance->typeId = $data['typeId'];
        $instance->category = $data['category'];
        $instance->name = $data['name'];
        $instance->timestamp = Carbon::parse($data['timestamp']);
        $instance->userId = $data['userId'];

        if (true === isset($data['state'])) {
            self::validateState($data['state']);
            $instance->state = (string) $data['state'];
        }

        return $instance;
    }

    /**
     * @phpstan-ignore-next-line
     * @throws AssertionFailedException|JsonException
     */
    public static function fromJson(string $data): self
    {
        return self::fromArray(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getTypeId(): string
    {
        return $this->typeId;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->getVersion(),
            'typeId' => $this->getTypeId(),
            'category' => $this->getCategory(),
            'name' => $this->getName(),
            'userId' => $this->getUserId(),
            'timestamp' => $this->timestamp->getTimestamp(),
            'state' => $this->getState(),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public static function validateState(string $state): void
    {
        Assertion::inArray($state, [self::STATE_ACTIVE, self::STATE_GRACE, self::STATE_INACTIVE]);
    }
}
