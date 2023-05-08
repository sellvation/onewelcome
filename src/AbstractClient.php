<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\Exceptions\OneWelcomeAPIException;

abstract class AbstractClient
{
    /**
     * @var APIClient
     */
    protected $client;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var null|string
     */
    protected $response;

    public function __construct(APIClient $client, Credentials $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
    }

    /**
     * @throws APIException
     */
    protected function request(string $requestMethod, string $url, array $headers = [], string $body = null): array
    {
        $request = new Request($requestMethod, $url, $headers, $body);

        try {
            $response = $this->client->executeWithAuthorizationHeader($this->credentials, $request, []);
        } catch (GuzzleException | JsonException | APIException | OneWelcomeAPIException $exception) {
            throw APIException::forErrorResponse($exception->getCode(), $exception->getMessage());
        }

        return $response;
    }
}
