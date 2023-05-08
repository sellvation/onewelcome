<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Exceptions;

use Exception;

class OneWelcomeAPIException extends Exception
{
    /**
     * @var string
     */
    private $error;

    public function __construct(string $error, string $message)
    {
        $this->error = $error;
        parent::__construct($message);
    }

    public static function fromClientException(int $code, string $message): self
    {
        return new self((string) $code, $message);
    }

    public function getError(): string
    {
        return $this->error;
    }
}
