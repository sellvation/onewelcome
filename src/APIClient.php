<?php

declare(strict_types=1);

namespace Sellvation\OneWelcome;

use Assert\AssertionFailedException;
use Sellvation\OneWelcome\Config\APIConfigInterface;
use Sellvation\OneWelcome\Exceptions\APIException;
use Sellvation\OneWelcome\Exceptions\OneWelcomeAPIException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class APIClient
{
    public const TOKEN_EXPIRATION_SECONDS = 3600;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var null|ResponseInterface
     */
    private $response;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException|APIException|JsonException|OneWelcomeAPIException
     */
    public function executeWithAuthorizationHeader(Credentials $credentials, Request $request, array $parameters): array
    {
        /** @var Request $requestWithAddedHeader */
        $requestWithAddedHeader = $request->withAddedHeader('Authorization', 'Bearer ' . $credentials->getAccessToken());
        $requestWithAddedHeader2 = $requestWithAddedHeader->withAddedHeader('x-plus-auth', 't7yw3jtw8478twy342t7834tyw3thwe78wyntwehnrt4wy8et7w3th');
        return $this->execute($requestWithAddedHeader2, $parameters);
    }

    /**
     * @throws GuzzleException|APIException|JsonException|OneWelcomeAPIException
     */
    public function execute(Request $request, array $parameters): array
    {
        try {
            $headers = $request->getHeaders();
            $headers['x-plus-auth'] = ['t7yw3jtw8478twy342t7834tyw3thwe78wyntwehnrt4wy8et7w3th'];
            $body = $request->getBody();
            $options = [];

            if (count($headers) > 0) {
                $options['headers'] = $headers;
            }

            if (count($parameters) > 0) {
                $options['form_params'] = $parameters;
            }

            if ($body->getSize() > 0) {
                $options['body'] = $body;
            }

            $this->response = $this->client->request($request->getMethod(), $request->getUri(), $options);
        } catch (ClientException $exception) {
            throw OneWelcomeAPIException::fromClientException($exception->getCode(), $exception->getResponse()->getBody()->getContents());
        }

        if (false === in_array($this->response->getStatusCode(), [200, 201, 204])) {
            throw new APIException("Invalid status code: " . $this->response->getStatusCode());
        }

        $body = (string) $this->response->getBody();

        if ('' === $body) {
            return [];
        }

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws APIException|AssertionFailedException
     */
    public function obtainCredentials(APIConfigInterface $apiConfig): Credentials
    {
        error_log('CIAM START CUSTOM LOGGING --');

        try {
            $request = new Request('POST', $apiConfig->getAuthenticationURL());
            $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
            $request = $request->withHeader('x-plus-auth', 't7yw3jtw8478twy342t7834tyw3thwe78wyntwehnrt4wy8et7w3th');
            
            error_log('CUSTOM NVK logging: '. json_encode($request));
            $response = $this->execute($request, [
                'grant_type' => $apiConfig->getGrantType(),
                'client_id' => $apiConfig->getClientId(),
                'client_secret' => $apiConfig->getClientSecret(),
                'scope' => $apiConfig->getScope(),
                'username' => $apiConfig->getUsername(),
                'password' => $apiConfig->getPassword(),
            ]);
        } catch (GuzzleException | APIException | OneWelcomeAPIException | JsonException | ClientException $exception) {

            error_log(json_encode($exception));
            error_log(json_encode($exception->getMessage()));

            throw APIException::errorObtainingToken($exception);
        }

        if (false === isset($response['access_token'])) {
            error_log(json_encode($response));
            error_log(json_encode($response->getStatusCode()));
            throw APIException::unexpectedResponse(implode(' ', $response));
        }

        error_log('-- NOTHING WENT WRONG WITH GENERATING TOKEN --');

        error_log('-- CIAM END OF CUSTOM LOGGING');

        return Credentials::fromOneWelcomeAPIResponse($response);
    }

    public function getOriginalResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
