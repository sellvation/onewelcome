<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome\Exceptions;

use Exception;

class APIException extends Exception
{
    public static function forErrorResponse(int $httpStatusCode, string $message): self
    {
        return new self(sprintf('Error making API request with HTTP status code %d and message "%s"', $httpStatusCode, $message));
    }

    public static function errorObtainingToken($previousException): self
    {
        return new self('Error obtaining token', 0, $previousException);
    }

    public static function unexpectedResponse(string $message): self
    {
        return new self(sprintf('Unexpected response from OneWelcome API while obtaining token. Response: "%s"', $message));
    }
}
